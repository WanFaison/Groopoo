<?php

namespace App\Repository;

use App\Entity\Liste;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Liste>
 */
class ListeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Liste::class);
    }


    public function findAllPaginated(int $page, int $limit, string $keyword): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('r.libelle LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
        $query = $queryBuilder->orderBy('r.id', 'ASC')
                            ->getQuery();
        
        return PaginatorService::pageInator($query, $page, $limit);
    }

//    /**
//     * @return Liste[] Returns an array of Liste objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Liste
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
