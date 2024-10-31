<?php

namespace App\Repository;

use App\Entity\Absence;
use App\Entity\Etudiant;
use App\Entity\Jour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Absence>
 */
class AbsenceRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Absence::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Absence $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findAllByJourAndEtudiant(?Jour $jour=null, ?Etudiant $etudiant=null):array
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if ($jour) {
            $queryBuilder->andWhere('r.jour = :jour')
                         ->setParameter('jour', $jour);
        }
        if ($etudiant) {
            $queryBuilder->andWhere('r.etudiant = :etudiant')
                         ->setParameter('etudiant', $etudiant);
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery()
                            ->getResult();

        return $query;
    }

    public function deleteById(int $id): void
    {
        $absence = $this->find($id);

        if ($absence) {
            $this->entityManager->remove($absence);
            $this->entityManager->flush();
        }
    }

//    /**
//     * @return Absence[] Returns an array of Absence objects
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

//    public function findOneBySomeField($value): ?Absence
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
