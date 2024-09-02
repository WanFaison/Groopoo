<?php

namespace App\Controller;

use App\Controller\Dto\Response\ListeResponseDto;
use App\Controller\Dto\RestResponse as DtoRestResponse;
use App\Entity\Liste;
use App\Repository\ListeRepository;
use RestResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListeController extends AbstractController
{
    #[Route('/api/liste', name: 'api_liste', methods: ['GET'])]
    public function listerListes(Request $request, ListeRepository $listeRepository): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');

        $listes = $listeRepository->findAllPaginated($page, $limit, $keyword);
        $dtos = [];
        foreach ($listes as $liste) {
            $dtos[] = (new ListeResponseDto())->toDto($liste);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'isArchived' => $r->isArchived(),
                'annee' => $r->getAnnee(),
                'ecole' => $r->getEcole(),
                'date' => $r->getDate()
            ];
        }

        $totalItems = $listes->count();
        $totalPages = ceil($totalItems / $limit);

        return DtoRestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }
}
