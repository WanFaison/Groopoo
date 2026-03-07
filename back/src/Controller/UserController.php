<?php

namespace App\Controller;

use App\Controller\Dto\RestResponse;
use App\Repository\EcoleRepository;
use App\Repository\UserRepository;
use App\Controller\Dto\Response\ProfileResponseDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Controller\Dto\Response\UserResponseDto;
use App\Entity\User;
use App\Enums\Role;
use App\Repository\ProfileRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $entityManager;
    private $userService;
    private $userRepository;
    private $ecoleRepository;
    public function __construct(EntityManagerInterface $entityManager, UserService $userService, UserRepository $userRepository, EcoleRepository $ecoleRepository)
    {
        $this->entityManager = $entityManager;
        $this->ecoleRepository = $ecoleRepository;
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    #[Route('/api/lister-users', name: 'api_users', methods: ['GET'])]
    public function listerUsers(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $role = $request->query->getString('role', '');
        $ecole = $request->query->getInt('ecole', 0);
        $arch = $request->query->getBoolean('arch', false);

        $users = $this->userRepository->findAllPaginated($page, $limit, $keyword, $role, $ecole, $arch);
        $results = $this->userService->userListeDto($users);

        $totalItems = $users->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/add-user', name: 'api_add_user', methods: ['POST'])]
    public function addUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'] ?? null;
        $prenom = $data['prenom'] ?? null;
        $email = $data['email'] ?? null;
        $ecole = $data['ecole'] ?? null;
        $profil = $data['profil'] ?? null;


        if($email){
            $user = $this->userRepository->createUser('passer', $email, $nom, $prenom);
            $user->setArchived(false);
            $user->addRole(Role::fromName($profil));
            if($profil=='ECOLE_ADMIN' || $profil=='COACH'){
                if(($ecole) && (count($ecole)>0)){
                    foreach($ecole as $e){
                        $user->addEcole($this->ecoleRepository->find($e));
                    }
                }
            }

            $this->userRepository->addOrUpdate($user);

            return RestResponse::requestResponse('user created!', $email, JsonResponse::HTTP_OK);
        }

        return RestResponse::requestResponse('failed to create', $email, JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/user-modif', name: 'api_user_modif', methods: ['GET'])]
    public function archiveUser(Request $request): JsonResponse
    {
        $userId = $request->query->getInt('user', 0);
        $motif = $request->query->getInt('motif', 0);

        $user = $this->userRepository->find($userId);
        if($user && $motif == 0){
            $user->setArchived(!$user->isArchived());
            $this->userRepository->addOrUpdate($user);
        }else{
            $this->userRepository->deleteById($userId);
        }
        
        return RestResponse::requestResponse('User has been updated', 1, JsonResponse::HTTP_OK);
    }

    #[Route('/api/modif-user', name: 'api_modif_user', methods: ['POST'])]
    public function updateUser(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uid = $data['id'] ?? null;
        $username = $data['username'] ?? null;
        $pswd1 = $data['pswd1'] ?? null;
        $pswd2 = $data['pswd2'] ?? null;

        $user = $this->userRepository->find($uid);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if(!empty($username)){
            if(($this->userRepository->findOneBy(['username' => $username])) && ($username != $user->getUsername())){
                return RestResponse::requestResponse('Ce username existe deja', 1, JsonResponse::HTTP_OK);
            }else{
                $user->setUsername($username);
            }
        }
        if(!empty($pswd1)){
            if ($passwordHasher->isPasswordValid($user, $pswd1)){
                $hashedPassword = $passwordHasher->hashPassword($user, $pswd2);
                $user->setPassword($hashedPassword);
            }else{
                return RestResponse::requestResponse('Mot de passe incorrect', 2, JsonResponse::HTTP_OK);
            }
        }

        $this->userRepository->addOrUpdate($user);
        return RestResponse::requestResponse('User updated', 0, JsonResponse::HTTP_OK);
    }
}
