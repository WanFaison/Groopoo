<?php

namespace App\Repository;

use App\Entity\Coach;
use App\Entity\Ecole;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coach>
 */
class CoachRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Coach::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Coach $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findAllByEcoleUnarchived(?Ecole $ecole): array
    {
        $queryBuilder = $this->createQueryBuilder('n');
        if ($ecole) {
            $queryBuilder->andWhere('n.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        $query = $queryBuilder->andWhere('n.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('n.id', 'ASC')
                            ->getQuery()
                            ->getResult();
        return $query;
    }

    public function findAllPaginatedByEcoleUnarchived(int $page, int $limit, string $keyword, ?Ecole $ecole): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('n');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('n.nom LIKE :keyword OR n.prenom LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%');
        }

        if ($ecole) {
            $queryBuilder->andWhere('n.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        $query = $queryBuilder->andWhere('n.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('n.id', 'ASC')
                            ->getQuery();
        return PaginatorService::pageInator($query, $page, $limit);
    }

    public function findAllUnarchived(): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.isArchived = :isArchived')
            ->setParameter('isArchived', false)
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult();
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
//     * @return Coach[] Returns an array of Coach objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Coach
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
