<?php

namespace App\Controller;

use App\Controller\Dto\Response\EcoleResponseDto;
use App\Controller\Dto\RestResponse;
use App\Repository\EcoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EcoleController extends AbstractController
{
    #[Route('/api/ecole', name: 'app_ecole', methods: ['GET'])]
    public function listerEcole(EcoleRepository $ecoleRepository): JsonResponse
    {
        $ecoles = $ecoleRepository->findAllUnarchived();
        $dtos = [];
        foreach ($ecoles as $niv) {
            $dtos[] = (new EcoleResponseDto())->toDto($niv);
        } 
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'isArchived' => $r->isArchived()
            ];
        }

        $totalItems = count($ecoles);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }
}
