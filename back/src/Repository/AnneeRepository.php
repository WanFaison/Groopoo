<?php

namespace App\Repository;

use App\Entity\Annee;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annee>
 */
class AnneeRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Annee::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Annee $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findByLibelle(string $libelle): ?Annee
    {
        return $this->createQueryBuilder('e')
        ->where('e.libelle = :libelle') 
        ->andWhere('e.isArchived = :isArchived') 
        ->setParameter('libelle', $libelle)
        ->setParameter('isArchived', false)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function checkExist(string $libelle): bool
    {
        $ent = $this->findByLibelle($libelle);
        if($ent){return true;}
        return false;
    }

    public function findAllPaginated(int $page, int $limit, string $keyword): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('r.libelle LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
        
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived') 
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery();
        
        return PaginatorService::pageInator($query, $page, $limit);
    }

//    /**
//     * @return Annee[] Returns an array of Annee objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Annee
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
