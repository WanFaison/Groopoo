<?php

namespace App\Repository;

use App\Entity\Liste;
use App\Entity\Annee;
use App\Entity\Ecole;
use App\Service\PaginatorService;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\Rule\AnyParameters;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Liste>
 */
class ListeRepository extends ServiceEntityRepository
{
    private $entityManager;
    private $anneeRepository;
    private $ecoleRepository;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, EcoleRepository $ecoleRepository, AnneeRepository $anneeRepository)
    {
        parent::__construct($registry, Liste::class);
        $this->entityManager = $entityManager;
        $this->ecoleRepository = $ecoleRepository;
        $this->anneeRepository = $anneeRepository;
    }


    public function findAllPaginated(int $page, int $limit, string $keyword, int $annee = null, int $ecole = null, int $archived = 0): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('r.libelle LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
        if ($annee) {
            $queryBuilder->andWhere('r.annee = :annee')
                         ->setParameter('annee', $this->anneeRepository->find($annee));
        }
        if ($ecole) {
            $queryBuilder->andWhere('r.ecole = :ecole')
                         ->setParameter('ecole', $this->ecoleRepository->find($ecole));
        }
        if($archived == 0){
            $query = $queryBuilder->andWhere('r.isArchived = :isArchived') 
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery();
        }else{
            $query = $queryBuilder->andWhere('r.isArchived = :isArchived') 
                            ->setParameter('isArchived', true)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery();
        }
        
        return PaginatorService::pageInator($query, $page, $limit);
    }

    public function findAllByAnnee(?Annee $annee = null):array
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if ($annee) {
            $queryBuilder->andWhere('r.annee = :annee')
                         ->setParameter('annee', $annee);
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery()
                            ->getResult();

        return $query;
    }

    public function findAllByEcole(?Ecole $ecole = null):array
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if ($ecole) {
            $queryBuilder->andWhere('r.ecole = :ecole')
                         ->setParameter('ecole', $ecole);
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery()
                            ->getResult();

        return $query;
    }

    public function findByLibelle(string $libelle): ?Liste
    {
        return $this->createQueryBuilder('e')
        ->where('e.libelle = :libelle') 
        ->andWhere('e.isArchived = :isArchived') 
        ->setParameter('libelle', $libelle)
        ->setParameter('isArchived', false)
        ->orderBy('e.id', 'DESC') 
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function addOrUpdate(Liste $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function deleteById(int $id): bool
    {
        $liste = $this->find($id);

        if (!$liste) {
            return false;
        }
        $this->entityManager->remove($liste);
        $this->entityManager->flush(); 

        return true;
    }

    public function convertToDate(string $dateString): DateTime
    {
        try {
            $dateTime = new \DateTime($dateString);
        } catch (\Exception $e) {
            echo 'Invalid date format: ' . $e->getMessage();
        }
        
        // Check if conversion was successful
        if ($dateTime instanceof \DateTimeInterface) {
            echo $dateTime->format('Y-m-d');
        }
        return $dateTime;
    }

//    /**
//     * @return Liste[] Returns an array of Liste objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Liste
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
