<?php

namespace App\Repository;

use App\Entity\Ecole;
use App\Entity\Filiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Filiere>
 */
class FiliereRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Filiere::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Filiere $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findAllByEcole(?Ecole $ecole=null): array
    {
        $queryBuilder = $this->createQueryBuilder('n');
        if ($ecole) {
            $queryBuilder->andWhere('n.ecole = :ecole')
                         ->setParameter('ecole', $ecole);
        }
        return $queryBuilder->andWhere('n.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('n.id', 'ASC')
                            ->getQuery()
                            ->getResult();
    }

    public function findByLibelle(string $libelle): ?Filiere
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
        $fil = $this->findByLibelle($libelle);
        if($fil){
            return true;
        }

        return false;
    }

//    /**
//     * @return Filiere[] Returns an array of Filiere objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Filiere
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
