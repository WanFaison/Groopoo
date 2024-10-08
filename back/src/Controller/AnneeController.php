<?php

namespace App\Controller;

use App\Controller\Dto\Response\AnneeResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Annee;
use App\Repository\AnneeRepository;
use App\Repository\ListeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/api/liste-annee', name: 'api_liste_annee', methods: ['GET'])]
    public function listerAnneePg(Request $request, AnneeRepository $anneeRepository): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');

        $annees = $anneeRepository->findAllPaginated($page, $limit, $keyword);
        
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

        $totalItems = $annees->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/add-annee', name: 'api_add_annee', methods: ['POST'])]
    public function addEcole(Request $request, AnneeRepository $anneeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $libelle = $data['data'] ?? null;

        if($libelle){
            $annee = new Annee();
            $annee->setLibelle($libelle);
            $annee->setArchived(false);
            $anneeRepository->addOrUpdate($annee);

            return RestResponse::requestResponse('annee created!', $libelle, JsonResponse::HTTP_OK);
        }

        return RestResponse::requestResponse('failed to create', $libelle, JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/annee-modif', name: 'api_annee_modif', methods: ['GET'])]
    public function archiveList(Request $request, AnneeRepository $anneeRepository, ListeRepository $listeRepository): JsonResponse
    {
        $anneeId = $request->query->getInt('annee', 0);
        $keyword = $request->query->getString('keyword', '');

        $annee = $anneeRepository->find($anneeId);
        if($keyword != ''){
            $annee->setLibelle($keyword);
        }else{
            $listes = $listeRepository->findAllByAnnee($annee);
            foreach ($listes as $l) {
                $l->setArchived(true);
                $listeRepository->addOrUpdate($l);
            }
            $annee->setArchived(true);
        }
        
        $anneeRepository->addOrUpdate($annee);
        
        return RestResponse::requestResponse('Annee has been updated', 1, JsonResponse::HTTP_OK);
    }
}
