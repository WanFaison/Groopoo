<?php

namespace App\Repository;

use App\Entity\Groupe;
use App\Entity\Liste;
use App\Service\PaginatorService;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Groupe>
 */
class GroupeRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Groupe::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Groupe $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findAllByListePaginated(int $page, int $limit, ?Liste $liste=null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if ($liste) {
            $queryBuilder->andWhere('r.liste = :liste')
                         ->setParameter('liste', $liste);
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery();

        return PaginatorService::pageInator($query, $page, $limit);
    }

//    /**
//     * @return Groupe[] Returns an array of Groupe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Groupe
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
