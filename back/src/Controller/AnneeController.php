<?php

namespace App\Controller;

use App\Controller\Dto\Response\AnneeResponseDto;
use App\Controller\Dto\RestResponse;
use App\Repository\AnneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AnneeController extends AbstractController
{
    #[Route('/api/annee', name: 'app_annee', methods: ['GET'])]
    public function listerAnnee(AnneeRepository $anneeRepository): JsonResponse
    {
        $annees = $anneeRepository->findAll();

        $dtos = [];
        foreach ($annees as $an) {
            $dtos[] = (new AnneeResponseDto())->toDto($an);
        } 
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'isArchived' => $r->isArchived()
            ];
        }

        $totalItems = count($annees);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }
}
