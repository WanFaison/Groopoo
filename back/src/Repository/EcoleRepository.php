<?php

namespace App\Repository;

use App\Entity\Ecole;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ecole>
 */
class EcoleRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Ecole::class);
        $this->entityManager = $entityManager;
    }

    public function addOrUpdate(Ecole $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findAllUnarchived(): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.isArchived = :isArchived')
            ->setParameter('isArchived', false)
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function checkExist(string $libelle): bool
    {
        $ent = $this->findByLibelle($libelle);
        if($ent){return true;}
        return false;
    }

    public function findByLibelle(string $libelle): ?Ecole
    {
        return $this->createQueryBuilder('e')
        ->where('e.libelle = :libelle') 
        ->andWhere('e.isArchived = :isArchived') 
        ->setParameter('libelle', $libelle)
        ->setParameter('isArchived', false)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function findAllPaginated(int $page, int $limit, string $keyword): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('r.libelle LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
        
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived') 
                            ->setParameter('isArchived', false)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery();
        
        return PaginatorService::pageInator($query, $page, $limit);
    }

//    /**
//     * @return Ecole[] Returns an array of Ecole objects
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

//    public function findOneBySomeField($value): ?Ecole
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
