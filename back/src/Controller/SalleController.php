<?php

namespace App\Controller;

use App\Controller\Dto\Response\SalleResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Salle;
use App\Repository\EcoleRepository;
use App\Repository\EtageRepository;
use App\Repository\ListeRepository;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SalleController extends AbstractController
{
    private $entityManager;
    private $ecoleRepository;
    private $etageRepository;
    private $salleRepository;
    private $listeRepository;

    public function __construct(EntityManagerInterface $entityManager, SalleRepository $salleRepository, EcoleRepository $ecoleRepository, EtageRepository $etageRepository, ListeRepository $listeRepository)
    {
        $this->entityManager = $entityManager;
        $this->ecoleRepository = $ecoleRepository;
        $this->salleRepository = $salleRepository;
        $this->etageRepository = $etageRepository;
        $this->listeRepository = $listeRepository;
    }

    #[Route('/api/liste-salle', name: 'app_salle_liste', methods: ['GET'])]
    public function listerSallesPg(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $ecoleId = $request->query->getInt('ecole', 0);

        $ecole = $ecoleId>0 ? $this->ecoleRepository->find($ecoleId) : null;
        $salles = $this->salleRepository->findAllPaginatedByEcoleUnarchived($page, $limit, $keyword, $ecole);
        $dtos = [];
        foreach ($salles as $salle) {
            $dtos[] = (new SalleResponseDto())->toDto($salle);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'ecole' => $r->getEcole(),
                'etage' => $r->getEtage()
            ];
        }

        $totalItems = $salles->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/get-salle', name: 'app_salle_liste_ecole', methods: ['GET'])]
    public function listerSallesEcole(Request $request): JsonResponse
    {
        $ecoleId = $request->query->getInt('ecole', 0);
        $ecole = $ecoleId>0 ? $this->ecoleRepository->find($ecoleId) : null;
        $salles = $this->salleRepository->findAllByEcoleUnarchived($ecole);
        $dtos = [];
        foreach ($salles as $salle) {
            $dtos[] = (new SalleResponseDto())->toDto($salle);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'ecole' => $r->getEcole(),
                'etage' => $r->getEtage()
            ];
        }

        $totalItems = count($salles);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/get-salle-active', name: 'app_salle_liste_active', methods: ['GET'])]
    public function listerSallesEcoleActive(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);

        $salles = [];
        foreach($liste->getGroupes() as $grp){
            in_array($grp->getSalle(), $salles, false)? null:$cc=$grp->getSalle();
            $cc ? $salles[] = $cc : null;
        }
        $dtos = [];
        foreach ($salles as $salle) {
            $dtos[] = (new SalleResponseDto())->toDto($salle);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'ecole' => $r->getEcole(),
                'etage' => $r->getEtage()
            ];
        }

        $totalItems = count($salles);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/salle-modif', name: 'api_salle_modif', methods: ['GET'])]
    public function modifSalle(Request $request): JsonResponse
    {
        $salleId = $request->query->getInt('salle', 0);
        $keyword = $request->query->getString('keyword', '');

        $salle = $this->salleRepository->find($salleId);
        if($keyword == ''){
            $salle->setArchived(true);
        }else{
            $salles = $this->salleRepository->findAllByEcoleUnarchived($salle->getEtage()->getEcole());
            foreach($salles as $s){
                if($s->getLibelle() == $keyword){
                    return RestResponse::requestResponse('Cette salle existe deja', 1, JsonResponse::HTTP_OK);
                }
            }
            $salle->setLibelle($keyword);
        }
        
        $this->salleRepository->addOrUpdate($salle);
        
        return RestResponse::requestResponse('Salle has been updated', 0, JsonResponse::HTTP_OK);
    }

    #[Route('/api/add-salle', name: 'api_add_salle', methods: ['POST'])]
    public function addSalle(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $libelle = $data['libelle'] ?? null;
        $etageId = $data['etage'] ?? null;

        $etage = $this->etageRepository->find($etageId);
        $ecole = $etage->getEcole();
        $salles = $this->salleRepository->findAllByEcoleUnarchived($ecole);
        foreach($salles as $s){
            if($s->getLibelle() == $libelle){
                return RestResponse::requestResponse('Cette salle existe deja', 1, JsonResponse::HTTP_OK);
            }
        }
        if($libelle){
            $salle = new Salle();
            $salle->setLibelle($libelle);
            $salle->setEtage($etage);
            $salle->setEcole($ecole);
            $salle->setArchived(false);
            $this->salleRepository->addOrUpdate($salle);

            return RestResponse::requestResponse('salle created!', 0, JsonResponse::HTTP_OK);
        }

        return RestResponse::requestResponse('failed to create', $libelle, JsonResponse::HTTP_BAD_REQUEST);
    }
}
