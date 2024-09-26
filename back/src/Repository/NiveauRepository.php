<?php

namespace App\Repository;

use App\Entity\Niveau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Niveau>
 */
class NiveauRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Niveau::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Niveau $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
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

    public function findByLibelle(string $libelle): ?Niveau
    {
        return $this->createQueryBuilder('e')
        ->where('e.libelle = :libelle') 
        ->andWhere('e.isArchived = :isArchived') 
        ->setParameter('libelle', $libelle)
        ->setParameter('isArchived', false)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function checkExistByLibelle(string $libelle): bool
    {
        $niv = $this->findByLibelle($libelle);
        if($niv){
            return true;
        }

        return false;
    }

//    /**
//     * @return Niveau[] Returns an array of Niveau objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Niveau
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
