<?php

namespace App\Repository;

use App\Entity\Theme;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Theme>
 */
class ThemeRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Theme::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Theme $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function checkExistByLibelle(string $libelle): bool
    {
        $theme = $this->findByLibelle($libelle);
        if($theme){ return true; }
        return false;
    }

    public function findByLibelle(string $libelle): ?Theme
    {
        return $this->createQueryBuilder('t')
        ->where('t.libelle = :libelle') 
        ->andWhere('t.isArchived = :isArchived') 
        ->setParameter('libelle', $libelle)
        ->setParameter('isArchived', false)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function findAll(): array
    {
        $queryBuilder = $this->createQueryBuilder('t');
        return $queryBuilder->andWhere('t.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('t.id', 'ASC')
                            ->getQuery()
                            ->getResult();
    }

    public function findAllPaginatedUnarchived(int $page, int $limit, string $keyword): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('n');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('n.libelle LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        $query = $queryBuilder->andWhere('n.isArchived = :isArchived')
                            ->setParameter('isArchived', false)
                            ->orderBy('n.id', 'ASC')
                            ->getQuery();
        return PaginatorService::pageInator($query, $page, $limit);
    }

    

//    /**
//     * @return Theme[] Returns an array of Theme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Theme
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
