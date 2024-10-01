<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Etudiant>
 */
class EtudiantRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Etudiant::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Etudiant $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function deleteById(int $id): bool
    {
        $etd = $this->find($id);

        if (!$etd) {
            return false;
        }
        $this->entityManager->remove($etd);
        $this->entityManager->flush(); 

        return true;
    }

    public function findByMatricule(string $matricule): ?Etudiant
    {
        return $this->createQueryBuilder('e')
                    ->where('e.matricule = :matricule') 
                    ->andWhere('e.isArchived = :isArchived') 
                    ->setParameter('matricule', $matricule)
                    ->setParameter('isArchived', false)
                    ->orderBy('e.id', 'DESC')  
                    ->setMaxResults(1)         
                    ->getQuery()
                    ->getOneOrNullResult(); 
    }

    public function checkExist(string $matricule): bool
    {
        $etd = $this->findByMatricule($matricule);
        if($etd){
            return true;
        }

        return false;
    }

//    /**
//     * @return Etudiant[] Returns an array of Etudiant objects
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

//    public function findOneBySomeField($value): ?Etudiant
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
