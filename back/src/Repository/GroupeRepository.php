<?php

namespace App\Repository;

use App\Entity\Groupe;
use App\Entity\Liste;
use App\Entity\Salle;
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

    public function deleteById(int $id): bool
    {
        $grp = $this->find($id);

        if (!$grp) {
            return false;
        }
        $this->entityManager->remove($grp);
        $this->entityManager->flush(); 

        return true;
    }

    public function findAllByListeGroupeSallePaginated(int $page, int $limit, ?Liste $liste=null, ?Groupe $groupe=null, ?Salle $salle=null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if ($liste) {
            $queryBuilder->andWhere('r.liste = :liste')
                         ->setParameter('liste', $liste);
        }
        if ($groupe) {
            $queryBuilder->andWhere('r.id = :id')
                         ->setParameter('id', $groupe->getId());
        }
        if ($salle) {
            $queryBuilder->andWhere('r.salle = :salle')
                         ->setParameter('salle', $salle);
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery();

        return PaginatorService::pageInator($query, $page, $limit);
    }

    public function findAllByListe(?Liste $liste=null):array
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if ($liste) {
            $queryBuilder->andWhere('r.liste = :liste')
                         ->setParameter('liste', $liste);
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery()
                            ->getResult();

        return $query;
    }

    public function findAllFinalByListe(?Liste $liste=null):array
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if ($liste) {
            $queryBuilder->andWhere('r.liste = :liste')
                         ->setParameter('liste', $liste);
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->andWhere('r.isFinal = :isFinal')
                            ->setParameter('isFinal', true)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery()
                            ->getResult();

        return $query;
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
