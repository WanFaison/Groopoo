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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/api/lister-users', name: 'api_users', methods: ['GET'])]
    public function listerUsers(Request $request, UserRepository $userRepository, EcoleRepository $ecoleRepository): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $ecole = $request->query->getInt('ecole', 0);

        if($ecole == 0){$ecole = null;} 
        $users = $userRepository->findAllPaginated($page, $limit, $keyword, $ecole);
        $results = $this->userListeDto($users);

        $totalItems = $users->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    private function userListeDto($users):array
    {
        $dtos = [];
        foreach($users as $u){
            $roles = [];
            foreach($u->getRoles() as $r){
                $roles[] = $r;
            }

            $dtos[] = (new UserResponseDto())->toDto($u, $roles);
        }

        $results = [];
        foreach($dtos as $d){
            $results[] = [
                'id' => $d->getId(),
                'email' => $d->getEmail(),
                'ecole' => $d->getEcole(),
                'ecoleT' => $d->getEcoleT(),
                'roles' => $d->getRoles(), 
            ];
        }

        return $results;
    }

    #[Route('/api/add-user', name: 'api_add_user', methods: ['POST'])]
    public function addEcole(Request $request, UserRepository $userRepository, EcoleRepository $ecoleRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $ecole = $data['ecole'] ?? null;
        $option1 = $data['option1'] ?? null;
        $option2 = $data['option2'] ?? null;
        $option3 = $data['option3'] ?? null;


        if($email){
            $user = $userRepository->createUser('passer', $email, $email);
            $user->setArchived(false);
            
            if($option1){$user->addRole(Role::ADMIN);}
            else if($option2){
                $user->addRole(Role::ECOLE_ADMIN);
                if(($ecole) && (count($ecole)>0)){
                    foreach($ecole as $e){
                        $user->addEcole($ecoleRepository->find($e));
                    }
                }
            }
            else{$user->addRole(Role::VISITEUR);}

            $userRepository->addOrUpdate($user);

            return RestResponse::requestResponse('user created!', $email, JsonResponse::HTTP_OK);
        }

        return RestResponse::requestResponse('failed to create', $email, JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/user-modif', name: 'api_user_modif', methods: ['GET'])]
    public function archiveList(Request $request, UserRepository $userRepository): JsonResponse
    {
        $userId = $request->query->getInt('user', 0);
        //$keyword = $request->query->getString('keyword', '');

        $user = $userRepository->find($userId);
        if($user){
            $user->setArchived(true);
            $userRepository->addOrUpdate($user);
        }
        
        return RestResponse::requestResponse('User has been updated', 1, JsonResponse::HTTP_OK);
    }

    #[Route('/api/modif-user/{id}', name: 'api_modif_user', methods: ['POST'])]
    public function updateUser(Request $request, UserRepository $userRepository, int $id, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;
        $pswd1 = $data['pswd1'] ?? null;
        $pswd2 = $data['pswd2'] ?? null;

        $user = $userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if(!empty($username)){
            if(($userRepository->findOneBy(['username' => $username])) && ($username != $user->getUsername())){
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

        $userRepository->addOrUpdate($user);
        return RestResponse::requestResponse('User updated', 0, JsonResponse::HTTP_OK);
    }
}
