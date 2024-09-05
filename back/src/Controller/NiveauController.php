<?php

namespace App\Controller;

use App\Controller\Dto\Response\NiveauResponseDto;
use App\Controller\Dto\RestResponse;
use App\Repository\NiveauRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NiveauController extends AbstractController
{
    #[Route('/api/niveau', name: 'app_niveau', methods: ['GET'])]
    public function listerNiveaux(NiveauRepository $niveauRepository): JsonResponse
    {
        $niveaux = $niveauRepository->findAllUnarchived();
        $dtos = [];
        foreach ($niveaux as $niv) {
            $dtos[] = (new NiveauResponseDto())->toDto($niv);
        } 
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'isArchived' => $r->isArchived()
            ];
        }

        $totalItems = count($niveaux);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }
}
