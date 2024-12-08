<?php

namespace App\Controller;

use App\Controller\Dto\Response\CoachResponseDto;
use App\Controller\Dto\Response\GroupeResponseDto;
use App\Controller\Dto\Response\JuryResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Jury;
use App\Repository\CoachRepository;
use App\Repository\JuryRepository;
use App\Repository\ListeRepository;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class JuryController extends AbstractController
{
    private $entityManager;
    private $juryRepository;
    private $coachRepository;
    private $listeRepository;
    private $salleRepository;

    public function __construct(EntityManagerInterface $entityManager, SalleRepository $salleRepository, JuryRepository $juryRepository, CoachRepository $coachRepository, ListeRepository $listeRepository)
    {
        $this->entityManager = $entityManager;
        $this->juryRepository = $juryRepository;
        $this->coachRepository = $coachRepository;
        $this->listeRepository = $listeRepository;
        $this->salleRepository = $salleRepository;
    }

    #[Route('/api/all-jury', name: 'app_all_jury', methods: ['GET'])]
    public function getAllJuryButOne(Request $request): JsonResponse
    {
        $coachId = $request->query->getInt('coach', 0);
        $listeId = $request->query->getInt('liste', 0);
        $coach = $coachId!=0 ? $this->coachRepository->find($coachId) : null;
        $liste = $this->listeRepository->find($listeId);
        $jurys = []; 
        foreach($liste->getJuries() as $jury){
            $cnt = 0;
            foreach($jury->getCoaches() as $cc){ $cc==$coach ? $cnt++ : null; }
            $cnt>0 ? null: $jurys[] = $jury;
        }

        $dtos = [];
        foreach ($jurys as $j) { $dtos[] = (new JuryResponseDto())->toDto($j, [], []); } 
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle()
            ];
        }

        $totalItems = count($jurys);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/coach-transfer', name: 'api_coach_transfer', methods: ['GET'])]
    public function transferCoach(Request $request): JsonResponse
    {
        $coachId = $request->query->getInt('coach', 0);
        $juryId = $request->query->getInt('jury', 0);
        $coach = $this->coachRepository->find($coachId);
        $jury = $this->juryRepository->find($juryId);

        if($coach && $jury){
            $jury->addCoach($coach);
            $this->juryRepository->addOrUpdate($jury);
            $this->coachRepository->addOrUpdate($coach);
            return RestResponse::requestResponse('coach transferer avec succes', 0, JsonResponse::HTTP_OK);
        }else{
            return RestResponse::requestResponse('coach ou jury non-trouve', 1, JsonResponse::HTTP_OK);
        }
    }

    #[Route('/api/liste-jury', name: 'api_liste_jury', methods: ['GET'])]
    public function listerJury(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $listeId = $request->query->getInt('liste', 0);
        //$salleId = $request->query->getInt('salle', 0);

        $liste = $this->listeRepository->find($listeId);
        $jurys = $this->juryRepository->findAllByListePaginated($page, $limit, $keyword, $liste);
        $results = $this->juryCoachesDto($jurys);

        $totalItems = count($results);
        $totalPages = $totalItems>0 ? ceil($totalItems / $limit):0;

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    private function juryCoachesDto($jurys):array
    {
        $dtos = [];
        foreach($jurys as $jury){
            $ccs = [];
            foreach($jury->getCoaches() as $coach){ $ccs[] = (new CoachResponseDto())->toDto($coach); }
            $coachs = [];
            foreach($ccs as $coach){
                $coachs[] = [
                    'id' => $coach->getId(),
                    'nom' => $coach->getNom(),
                    'prenom' => $coach->getPrenom(),
                    'tel' => $coach->getTel(),
                    'email' => $coach->getEmail(),
                    'etat' => $coach->getEtat(),
                    'ecole' => $coach->getEcole()
                ];
            }

            $grps = [];
            foreach($jury->getGroupes() as $group){ $grps[] = (new GroupeResponseDto())->toDto($group, []); }
            $groups = [];
            foreach($grps as $group){
                $groups[] = [
                    'id' => $group->getId(),
                    'libelle' => $group->getLibelle()
                ];
            }

            $dtos[] = (new JuryResponseDto())->toDto($jury, $coachs, $groups);
        }

        $results = [];
        foreach($dtos as $d){
            $results[] = [
                'id' => $d->getId(),
                'libelle' => $d->getLibelle(),
                'coachs' => $d->getCoachs(),
                'groupes' => $d->getGroupes()
            ];
        }

        return $results;
    }
}
