<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FIliereController extends AbstractController
{
    #[Route('/f/iliere', name: 'app_f_iliere')]
    public function index(): Response
    {
        return $this->render('f_iliere/index.html.twig', [
            'controller_name' => 'FIliereController',
        ]);
    }
}
