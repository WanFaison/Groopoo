<?php

namespace App\Repository;

use App\Entity\Liste;
use App\Entity\Annee;
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
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Liste::class);
        $this->entityManager = $entityManager;
    }


    public function findAllPaginated(int $page, int $limit, string $keyword, ?Annee $annee = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('r.libelle LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
        if ($annee) {
            $queryBuilder->andWhere('r.annee = :annee')
                         ->setParameter('annee', $annee);
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived') 
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery();
        
        return PaginatorService::pageInator($query, $page, $limit);
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
