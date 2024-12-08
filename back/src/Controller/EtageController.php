<?php

namespace App\Controller;

use App\Controller\Dto\Response\EtageResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Etage;
use App\Repository\EcoleRepository;
use App\Repository\EtageRepository;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EtageController extends AbstractController
{
    private $entityManager;
    private $ecoleRepository;
    private $etageRepository;
    private $salleRepository;

    public function __construct(EntityManagerInterface $entityManager, SalleRepository $salleRepository, EcoleRepository $ecoleRepository, EtageRepository $etageRepository)
    {
        $this->entityManager = $entityManager;
        $this->ecoleRepository = $ecoleRepository;
        $this->salleRepository = $salleRepository;
        $this->etageRepository = $etageRepository;
    }

    #[Route('/api/all-etage', name: 'app_etage_all', methods: ['GET'])]
    public function findAllByEcole(Request $request): JsonResponse
    {
        $ecoleId = $request->query->getInt('ecole', 0);
        $ecole = $ecoleId>0 ? $this->ecoleRepository->find($ecoleId) : null;

        $etages = $this->etageRepository->findAllByEcoleUnarchived($ecole);
        $dtos=[];
        foreach ($etages as $etage){
            $dtos[] = (new EtageResponseDto())->toDto($etage);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'ecole' => $r->getEcole()
            ];
        }

        $totalItems = count($etages);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/liste-etage', name: 'app_etage_liste', methods: ['GET'])]
    public function listerEtages(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $ecoleId = $request->query->getInt('ecole', 0);

        $ecole = $ecoleId>0 ? $this->ecoleRepository->find($ecoleId) : null;
        $etages = $this->etageRepository->findAllPaginatedByEcoleUnarchived($page, $limit, $keyword, $ecole);
        $dtos = [];
        foreach ($etages as $etage) {
            $dtos[] = (new EtageResponseDto())->toDto($etage);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle(),
                'ecole' => $r->getEcole()
            ];
        }

        $totalItems = $etages->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/add-etage', name: 'api_add_etage', methods: ['POST'])]
    public function addEtage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $libelle = $data['libelle'] ?? null;
        $ecoleId = $data['ecole'] ?? null;

        $ecole = $this->ecoleRepository->find($ecoleId);
        $etages = $this->etageRepository->findAllByEcoleUnarchived($ecole);
        foreach($etages as $s){
            if($s->getLibelle() == $libelle){
                return RestResponse::requestResponse('Cette etage existe deja', 1, JsonResponse::HTTP_OK);
            }
        }
        if($libelle){
            $etage = new Etage();
            $etage->setLibelle($libelle)
                    ->setEcole($ecole)
                    ->setArchived(false);
            $this->etageRepository->addOrUpdate($etage);

            return RestResponse::requestResponse('etage created!', 0, JsonResponse::HTTP_OK);
        }

        return RestResponse::requestResponse('failed to create', $libelle, JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/etage-modif', name: 'api_etage_modif', methods: ['GET'])]
    public function modifEtage(Request $request): JsonResponse
    {
        $etageId = $request->query->getInt('etage', 0);
        $keyword = $request->query->getString('keyword', '');

        $etage = $this->etageRepository->find($etageId);
        if($keyword == ''){
            $etage->setArchived(true);
        }else{
            $etages = $this->etageRepository->findAllByEcoleUnarchived($etage->getEcole());
            foreach($etages as $s){
                if($s->getLibelle() == $keyword){
                    return RestResponse::requestResponse('Cette etage existe deja', 1, JsonResponse::HTTP_OK);
                }
            }
            $etage->setLibelle($keyword);
        }
        
        $this->etageRepository->addOrUpdate($etage);
        
        return RestResponse::requestResponse('etage has been updated', 0, JsonResponse::HTTP_OK);
    }
}
