<?php

namespace App\Repository;

use App\Entity\Classe;
use App\Entity\Ecole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Classe>
 */
class ClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classe::class);
    }

    public function findAllByEcole(?Ecole $ecole=null): array
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if ($ecole) {
            $queryBuilder->andWhere('r.ecole = :ecole')
                         ->setParameter('ecole', $ecole);
        }
        return $queryBuilder->andWhere('r.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery()
                            ->getResult();
    }

//    /**
//     * @return Classe[] Returns an array of Classe objects
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

//    public function findOneBySomeField($value): ?Classe
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
