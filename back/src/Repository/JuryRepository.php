<?php

namespace App\Repository;

use App\Entity\Jury;
use App\Entity\Liste;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Jury>
 */
class JuryRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Jury::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Jury $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findAllNotFinalByListePaginated(int $page, int $limit, string $keyword, ?Liste $liste): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if ($liste) {
            $queryBuilder->andWhere('r.liste = :liste')
                ->setParameter('liste', $liste);
        }
        if (!empty($keyword)) {
            $queryBuilder->andWhere('r.libelle LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
        
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived') 
                            ->setParameter('isArchived', false)
                            ->andWhere('r.isFinal = :isFinal') 
                            ->setParameter('isFinal', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery();
        
        return PaginatorService::pageInator($query, $page, $limit);
    }

    public function findFinalistJuryByList(?Liste $liste)
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
                            ->setMaxResults(1)
                            ->getQuery()
                            ->getOneOrNullResult();
        return $query;
    }

//    /**
//     * @return Jury[] Returns an array of Jury objects
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

//    public function findOneBySomeField($value): ?Jury
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
