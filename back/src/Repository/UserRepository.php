<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Ecole;
use App\Enums\Role;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private $passwordHasher;
    private $entityManager;
    private $ecoleRepository;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, EcoleRepository $ecoleRepository)
    {
        parent::__construct($registry, User::class);
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->ecoleRepository = $ecoleRepository;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function createUser(string $password, string $email, string $nom, string $prenom){
        $user = new User();
        $username = mb_substr($prenom, 0, 1).''.$nom;
        $index = count($this->findAllByNameEcoleRole($username));
        $index > 0? $username = $username.''.$index : null;
        $user->setUsername($username)
            ->setEmail($email)
            ->setNom($nom)
            ->setPrenom($prenom);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        return $user;
    }
    
    public function findAllPaginated(int $page, int $limit, string $keyword, string $role='', int $ecole = 0, bool $arch = false): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('r.email LIKE :keyword OR r.nom LIKE :keyword OR r.prenom LIKE :keyword OR r.username LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
        if ($ecole>0) {
            $queryBuilder->join('r.ecoles', 'e')
                     ->andWhere('e.id = :ecoleId')
                     ->setParameter('ecoleId', $ecole);
        }
        if(!empty($role)){
            $queryBuilder->andWhere('r.roles LIKE :role')
                        ->setParameter('role', '%"'.$role.'"%');
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived') 
                            ->setParameter('isArchived', $arch)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery();
        
        return PaginatorService::pageInator($query, $page, $limit);
    }

    public function findAllByNameEcoleRole(string $keyword, string $role='', int $ecole = 0, bool $arch = false):array
    {
        $queryBuilder = $this->createQueryBuilder('r');
        if (!empty($keyword)) {
            $queryBuilder->andWhere('r.nom LIKE :keyword OR r.prenom LIKE :keyword OR r.username LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }
        if ($ecole>0) {
            $queryBuilder->join('r.ecoles', 'e')
                     ->andWhere('e.id = :ecoleId')
                     ->setParameter('ecoleId', $ecole);
        }
        if(!empty($role)){
            $queryBuilder->andWhere('r.roles LIKE :role')
                        ->setParameter('role', '%"'.$role.'"%');
        }
        $query = $queryBuilder->andWhere('r.isArchived = :isArchived') 
                            ->setParameter('isArchived', $arch)
                            ->orderBy('r.id', 'ASC')
                            ->getQuery()
                            ->getResult();

        return $query;
    }

    public function findAllByEcoleOrRole(Ecole $ecole, string $role, bool $archived = false): array
    {
        $queryBuilder = $this->createQueryBuilder('u');
        if($ecole){
            $queryBuilder->innerJoin('u.ecoles', 'e')
                        ->andWhere('e = :ecole')
                        ->setParameter('ecole', $ecole);
        }
        if(!empty($role)){
            $queryBuilder->andWhere('u.roles LIKE :role')
                        ->setParameter('role', '%"'.$role.'"%');
        }
        $query = $queryBuilder->andWhere('u.isArchived = :isArchived')
                    ->setParameter('isArchived', $archived)
                    ->orderBy('u.id', 'ASC')
                    ->getQuery()
                    ->getResult();

        return $query;
    }

    public function findAllCoachExceptOneById(int $coachId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.id != :id')
            ->setParameter('id', $coachId)
            ->andWhere('c.roles LIKE :role')
            ->setParameter('role', '%"'. Role::COACH->value .'"%')
            ->andWhere('c.isArchived = :isArchived')
            ->setParameter('isArchived', false)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function addOrUpdate(User $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function deleteById(int $id): bool
    {
        $user = $this->find($id);

        if (!$user) {
            return false;
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush(); 

        return true;
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
