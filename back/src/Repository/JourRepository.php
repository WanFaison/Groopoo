<?php

namespace App\Repository;

use App\Entity\Jour;
use App\Entity\Liste;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Jour>
 */
class JourRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Jour::class);
        $this->entityManager = $entityManager;
    }

    public function findAllByListeAndUnarchived(?Liste $liste): ?array
    {
        return $this->createQueryBuilder('e')
        ->where('e.liste = :liste') 
        ->andWhere('e.isArchived = :isArchived') 
        ->setParameter('liste', $liste)
        ->setParameter('isArchived', false)
        ->orderBy('e.id', 'ASC')
        ->getQuery()
        ->getResult();
    }

    public function addOrUpdate(Jour $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

//    /**
//     * @return Jour[] Returns an array of Jour objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Jour
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
