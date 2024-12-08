<?php

namespace App\Repository;

use App\Entity\Ecole;
use App\Entity\Etage;
use App\Entity\Salle;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Salle>
 */
class SalleRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Salle::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Salle $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findByLibelle(string $libelle): ?Salle
    {
        return $this->createQueryBuilder('e')
        ->where('e.libelle = :libelle') 
        ->andWhere('e.isArchived = :isArchived') 
        ->setParameter('libelle', $libelle)
        ->setParameter('isArchived', false)
        ->getQuery()
        ->getOneOrNullResult();
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

    public function findAllByEtageUnarchived(?Etage $etage): array
    {
        $queryBuilder = $this->createQueryBuilder('n');
        if ($etage) {
            $queryBuilder->andWhere('n.etage = :etage')
                ->setParameter('etage', $etage);
        }

        $query = $queryBuilder->andWhere('n.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('n.id', 'ASC')
                            ->getQuery()
                            ->getResult();
        return $query;
    }

//    /**
//     * @return Salle[] Returns an array of Salle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Salle
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
