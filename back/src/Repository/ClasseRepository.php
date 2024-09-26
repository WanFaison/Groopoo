<?php

namespace App\Repository;

use App\Entity\Classe;
use App\Entity\Ecole;
use App\Entity\Filiere;
use App\Entity\Niveau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Classe>
 */
class ClasseRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Classe::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Classe $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
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

    public function findByNiveauFiliere(?Niveau $niveau = null, ?Filiere $filiere = null): Classe
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if (($niveau) && ($filiere)) {
            $queryBuilder->andWhere('r.niveau = :niveau')
                         ->setParameter('niveau', $niveau)
                         ->andWhere('r.filiere = :filiere')
                         ->setParameter('filiere', $filiere);
        }
        
        return $queryBuilder->andWhere('r.isArchived = :isArchived') 
                            ->setParameter('isArchived', false)
                            ->getQuery()
                            ->getOneOrNullResult();
    }

    public function findByLibelle(string $libelle): ?Classe
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
        $classe = $this->findByLibelle($libelle);
        if($classe){
            return true;
        }

        return false;
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
