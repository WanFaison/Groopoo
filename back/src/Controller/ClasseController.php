<?php

namespace App\Controller;

use App\Controller\Dto\Response\ClasseResponseDto;
use App\Repository\ClasseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Dto\RestResponse;
use App\Repository\EcoleRepository;
use App\Repository\ListeRepository;

class ClasseController extends AbstractController
{
    private $classeRepository;
    private $ecoleRepository;
    private $listeRepository;

    public function __construct(ListeRepository $listeRepository, ClasseRepository $classeRepository, EcoleRepository $ecoleRepository)
    {
        $this->classeRepository = $classeRepository;
        $this->ecoleRepository = $ecoleRepository;
        $this->listeRepository = $listeRepository;
    }

    #[Route('/api/classe', name: 'app_classe', methods: ['GET'])]
    public function listerClasse(Request $request, ClasseRepository $classeRepository, EcoleRepository $ecoleRepository): JsonResponse
    {
        $ecole = $request->query->getInt('ecole', 0);
        $listeId = $request->query->getInt('liste', 0);
        $liste = $listeId!=0 ? $this->listeRepository->find($listeId) : null;

        if($ecole ==0){
            $classes = $classeRepository->findAllByEcole($liste->getEcole());
        }else{
            $classes = $classeRepository->findAllByEcole($ecoleRepository->find($ecole));
        }

        $dtos = [];
        foreach($classes as $classe) {
            $dtos[] = (new ClasseResponseDto($classe))->toDto($classe);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'filiere' => $r->getFiliere(),
                'niveau' => $r->getNiveau(),
                'effectif' => $r->getEffectif()
            ];
        }
        $totalItems = count($classes);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }


}
