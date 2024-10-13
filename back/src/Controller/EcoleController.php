<?php

namespace App\Controller;

use App\Controller\Dto\Response\EcoleResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Ecole;
use App\Repository\EcoleRepository;
use App\Repository\ListeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/api/liste-ecole', name: 'api_liste_ecole', methods: ['GET'])]
    public function listerEcolePg(Request $request, EcoleRepository $ecoleRepository): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');

        $ecoles = $ecoleRepository->findAllPaginated($page, $limit, $keyword);
        
        $dtos = [];
        foreach ($ecoles as $e) {
            $dtos[] = (new EcoleResponseDto())->toDto($e);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'isArchived' => $r->isArchived()
            ];
        }

        $totalItems = $ecoles->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/add-ecole', name: 'api_add_ecole', methods: ['POST'])]
    public function addEcole(Request $request, EcoleRepository $ecoleRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $libelle = $data['data'] ?? null;

        if($libelle){
            $ecole = new Ecole();
            $ecole->setLibelle($libelle);
            $ecole->setArchived(false);
            $ecoleRepository->addOrUpdate($ecole);

            return RestResponse::requestResponse('ecole created!', $libelle, JsonResponse::HTTP_OK);
        }

        return RestResponse::requestResponse('failed to create', $libelle, JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/ecole-modif', name: 'api_ecole_modif', methods: ['GET'])]
    public function archiveList(Request $request, EcoleRepository $ecoleRepository, ListeRepository $listeRepository): JsonResponse
    {
        $ecoleId = $request->query->getInt('ecole', 0);
        $keyword = $request->query->getString('keyword', '');

        $ecole = $ecoleRepository->find($ecoleId);
        if($keyword == ''){
            $listes = $listeRepository->findAllByEcole($ecole);
            if(count($listes)>0){
                foreach ($listes as $l) {
                    $l->setArchived(true);
                    $listeRepository->addOrUpdate($l);
                }
            }
            $ecole->setArchived(true);
        }else{
            $ecole->setLibelle($keyword);
        }
        
        $ecoleRepository->addOrUpdate($ecole);
        
        return RestResponse::requestResponse('Ecole has been updated', 1, JsonResponse::HTTP_OK);
    }
}
