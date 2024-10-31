<?php

namespace App\Controller;

use App\Controller\Dto\Response\FiliereResponseDto;
use App\Repository\FiliereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Dto\RestResponse;
use App\Repository\EcoleRepository;

class FiliereController extends AbstractController
{
    #[Route('/api/filiere', name: 'app_filiere', methods: ['GET'])]
    public function listerFiliere(Request $request, FiliereRepository $filiereRepository, EcoleRepository $ecoleRepository): JsonResponse
    {
        $ecole = $request->query->getInt('ecole', 0);

        if($ecole ==0){
            $filieres = $filiereRepository->findAllByEcole();
        }else{
            $filieres = $filiereRepository->findAllByEcole($ecoleRepository->find($ecole));
        }

        $dtos = [];
        foreach($filieres as $filiere) {
            $dtos[] = (new FiliereResponseDto($filiere))->toDto($filiere);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'isArchived' => $r->isArchived()
            ];
        }
        $totalItems = count($filieres);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }
}
