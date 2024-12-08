<?php

namespace App\Repository;

use App\Entity\Ecole;
use App\Entity\Etage;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etage>
 */
class EtageRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Etage::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Etage $entity): void
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
            $queryBuilder->andWhere('n.libelle LIKE :keyword')
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

//    /**
//     * @return Etage[] Returns an array of Etage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Etage
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
