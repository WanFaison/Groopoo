<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route(path: '/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasher $passwordHasher, AuthenticationUtils $authenticationUtils, SessionInterface $session, UserRepository $userRepository): Response
    {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            return $this->redirectToRoute('app_login', ['error' => 'User not found']);
        }

        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->redirectToRoute('app_login', ['error' => 'Invalid password']);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        $this->redirectToRoute("app_login");
    }
}
