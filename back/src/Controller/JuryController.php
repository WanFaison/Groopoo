<?php

namespace App\Controller;

use App\Controller\Dto\Response\CoachResponseDto;
use App\Controller\Dto\Response\FinalJuryResponseDto;
use App\Controller\Dto\Response\FinalThemeResponseDto;
use App\Controller\Dto\Response\GroupeResponseDto;
use App\Controller\Dto\Response\JuryResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Jury;
use App\Repository\CoachRepository;
use App\Repository\GroupeRepository;
use App\Repository\JuryRepository;
use App\Repository\ListeRepository;
use App\Repository\SalleRepository;
use App\Repository\ThemeRepository;
use App\Repository\UserRepository;
use App\Service\ExportService;
use App\Service\NoteService;
use App\Service\JuryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class JuryController extends AbstractController
{
    private $entityManager;
    private $juryRepository;
    private $userRepository;
    private $listeRepository;
    private $salleRepository;
    private $groupeRepository;
    private $themeRepository;
    private $noteService;
    private $exportService;
    private $juryService;

    public function __construct(EntityManagerInterface $entityManager, JuryService $juryService, ExportService $exportService, NoteService $noteService, ThemeRepository $themeRepository, GroupeRepository $groupeRepository, SalleRepository $salleRepository, JuryRepository $juryRepository, UserRepository $userRepository, ListeRepository $listeRepository)
    {
        $this->entityManager = $entityManager;
        $this->juryRepository = $juryRepository;
        $this->userRepository = $userRepository;
        $this->listeRepository = $listeRepository;
        $this->salleRepository = $salleRepository;
        $this->groupeRepository = $groupeRepository;
        $this->themeRepository = $themeRepository;
        $this->noteService = $noteService;
        $this->exportService = $exportService;
        $this->juryService = $juryService;
    }

    #[Route('/api/all-jury', name: 'app_all_jury', methods: ['GET'])]
    public function getAllJuryButOne(Request $request): JsonResponse
    {
        $coachId = $request->query->getInt('coach', 0);
        $listeId = $request->query->getInt('liste', 0);
        $coach = $coachId!=0 ? $this->userRepository->find($coachId) : null;
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
        $coach = $this->userRepository->find($coachId);
        $jury = $this->juryRepository->find($juryId);

        if($coach && $jury){
            $jury->addCoach($coach);
            $this->juryRepository->addOrUpdate($jury);
            $this->userRepository->addOrUpdate($coach);
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
        $jurys = $this->juryRepository->findAllNotFinalByListePaginated($page, $limit, $keyword, $liste);
        $results = $this->juryService->juryCoachesDto($jurys);

        $totalItems = count($results);
        $totalPages = $totalItems>0 ? ceil($totalItems / $limit):0;

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/remove-coach', name: 'api_remove_coach', methods: ['GET'])]
    public function removeCoach(Request $request): JsonResponse
    {
        $coachId = $request->query->getInt('coach', 0);
        $juryId = $request->query->getInt('jury', 0);
        $coach = $this->userRepository->find($coachId);
        $jury = $this->juryRepository->find($juryId);

        if($coach && $jury){
            $jury->removeCoach($coach);
            $this->juryRepository->addOrUpdate($jury);
            $this->userRepository->addOrUpdate($coach);
            return RestResponse::requestResponse('coach removed', 0, JsonResponse::HTTP_OK);
        }
        return RestResponse::requestResponse('coach or jury not found', 1, JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/final-jury', name: 'api_final_jury', methods: ['GET'])]
    public function showFinalJury(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);

        $finalJury = $this->juryRepository->findFinalistJuryByList($liste);
        $results = [];

        if($finalJury){
            $results = $this->juryService->juryFinalCoachesDto([$finalJury]);
        }else{
            $newJury = new Jury();
            $newJury->setListe($liste)
                    ->setArchived(false)
                    ->setFinal(true)
                    ->setLibelle('Jury Finaliste');
            $this->juryRepository->addOrUpdate($newJury);

            $results = $this->juryService->juryFinalCoachesDto([$newJury]);
        }
        
        $totalItems = count($results);
        return RestResponse::paginateResponse($results, 0, $totalItems, 1, JsonResponse::HTTP_OK);
    }

    

    #[Route('/api/final-export', name: 'api_final_export', methods: ['GET'])]
    public function exportExcelFinal(Request $request): BinaryFileResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $excelFile = $this->exportService->makeFinalJurySheet($liste);

        return new BinaryFileResponse($excelFile);
    }
    
}
