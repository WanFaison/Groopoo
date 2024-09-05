<?php

namespace App\Repository;

use App\Entity\Liste;
use App\Service\PaginatorService;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\Rule\AnyParameters;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Liste>
 */
class ListeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Liste::class);
    }


    public function findAllPaginated(int $page, int $limit, string $keyword, DateTimeInterface $startDate = null, DateTimeInterface $endDate = new DateTime()): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('r.libelle LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
        if ($startDate !== null) {
            $queryBuilder->andWhere('r.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);
        }
        $query = $queryBuilder->orderBy('r.id', 'ASC')
                            ->getQuery();
        
        return PaginatorService::pageInator($query, $page, $limit);
    }

    public function convertToDate(string $dateString): DateTime
    {
        try {
            $dateTime = new \DateTime($dateString);
        } catch (\Exception $e) {
            echo 'Invalid date format: ' . $e->getMessage();
        }
        
        // Check if conversion was successful
        if ($dateTime instanceof \DateTimeInterface) {
            echo $dateTime->format('Y-m-d');
        }
        return $dateTime;
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
