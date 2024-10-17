<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, JWTTokenManagerInterface $JWTManager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $user = $userRepository->findOneBy(['username' => $username]);
        if (!$user) {
            $user = $userRepository->findOneBy(['email' => $username]);
        }
        if ((!$user) || ($user->isArchived())) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $token = $JWTManager->create($user);
            dump($token);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Token creation failed: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        if($user->getEcole()){$ecoleId = $user->getEcole()->getId();}
        else{$ecoleId = 0;}
        $userDto = [
            'id'=>$user->getId(),
            'username'=>$user->getUsername(),
            'role'=>$user->getRoles()[0],
            'ecole'=>$ecoleId
        ];
        // Return a response, typically with a JWT or session token in real applications
        return new JsonResponse(['message' => 'Authentication successful',
                                'status' => JsonResponse::HTTP_OK,
                                'token' => $token,
                                'user' => $userDto], JsonResponse::HTTP_OK);
    
    }
}
