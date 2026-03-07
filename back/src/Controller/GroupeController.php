<?php

namespace App\Controller;

use App\Controller\Dto\Request\GroupeRequestDto;
use App\Controller\Dto\Response\FinalGroupeResponseDto;
use App\Controller\Dto\RestResponse;
use App\Enums\Role;
use App\Repository\EcoleRepository;
use App\Repository\GroupeRepository;
use App\Repository\JourRepository;
use App\Repository\ListeRepository;
use App\Repository\CoachRepository;
use App\Repository\UserRepository;
use App\Service\CoachService;
use App\Service\ExportService;
use App\Service\NoteService;
use App\Service\GroupeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GroupeController extends AbstractController
{
    private $ecoleRepository;
    private $groupeRepository;
    private $listeRepository;
    private $entityManager;
    private $jourRepository;
    private $userRepository;
    private $noteService;
    private $groupeService;
    private $coachService;
    private $exportService;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, ExportService $exportService, CoachService $coachService, GroupeService $groupeService, NoteService $noteService, JourRepository $jourRepository, EcoleRepository $ecoleRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->entityManager = $entityManager;
        $this->ecoleRepository = $ecoleRepository;
        $this->groupeRepository = $groupeRepository;
        $this->listeRepository = $listeRepository;
        $this->jourRepository = $jourRepository;
        $this->userRepository = $userRepository;
        $this->noteService = $noteService;
        $this->groupeService = $groupeService;
        $this->coachService = $coachService;
        $this->exportService = $exportService;
    }

    #[Route('/api/all-groupe', name: 'app_all_groupe', methods: ['GET'])]
    public function listerAllGroupe(Request $request): JsonResponse
    {
        $liste = $request->query->getInt('liste', 0);
        $coachId = $request->query->getInt('coach', 0);

        $coach = $this->userRepository->find($coachId);
        if(in_array(Role::ADMIN, $coach->getRoles()) || in_array(Role::ECOLE_ADMIN, $coach->getRoles())){
            $groupes = $this->groupeRepository->findAllByListe($this->listeRepository->find($liste));
        }else{
            $groupes = $this->groupeRepository->findAllByCoachListe($coach, $this->listeRepository->find($liste));
        }

        $results = [];
        foreach ($groupes as $g) {
            $dto = (new GroupeRequestDto())->toDto($g);
            $results[] = [
                'id' => $dto->getId(),
                'libelle' => $dto->getLibelle()
            ];
        }  
        $totalItems = count($groupes);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/etd-groupe', name: 'api_groupe_etd', methods: ['GET'])]
    public function voirEtdGroupe(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 1);
        $jourId = $request->query->getInt('jour', 0);
        $groupeId = $request->query->getInt('groupe', 0);
        $coachId = $request->query->getInt('coach', 0);

        $jour = $this->jourRepository->find($jourId);
        $coach = $this->userRepository->find($coachId);
        if(in_array(Role::ADMIN, $coach->getRoles()) || in_array(Role::ECOLE_ADMIN, $coach->getRoles())){
            $groupes = $this->groupeRepository->findAllByListeGroupeSallePaginated($page, $limit, $jour->getListe(), $this->groupeRepository->find($groupeId));
        }else{
            $groupes = $this->groupeRepository->findAllByCoachListePaginated($page, $limit, $coach, $jour->getListe(), $this->groupeRepository->find($groupeId));
        }

        $results = $this->groupeService->grpJourDto($groupes, $jour);
        $totalItems = $groupes->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }


    #[Route('/api/liste-groupe', name: 'api_groupe_liste', methods: ['GET'])]
    public function listeGroupe(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $liste = $request->query->getInt('liste', 0);
        $groupes = $this->groupeRepository->findAllByListeGroupeSallePaginated($page, $limit, $this->listeRepository->find($liste));

        $results = $this->groupeService->grpListeDto($groupes);
        $totalItems = $groupes->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    #[Route('/api/remove-empty-groups', name: 'app_remove_empty_groups', methods: ['GET'])]
    public function removeEmptyGroups(): JsonResponse
    {
        $groups = $this->groupeRepository->findGroupesWithoutEtudiants();
        foreach ($groups as $g) {
            $this->entityManager->remove($g); 
        }
        $this->entityManager->flush();

        return RestResponse::requestResponse('Empty groups have been removed', 0, JsonResponse::HTTP_OK);
    }


    #[Route('/api/notes', name: 'app_notes', methods: ['POST'])]
    public function setNotes(Request $request): JsonResponse
    {
        if (!$request->getContent()) {
            return new JsonResponse(['error' => 'No content'], 400);
        }
        $data = json_decode($request->getContent(), true);
        $notes = $data['notes'] ?? [];

        if (!is_array($notes)) {
            return new JsonResponse(['error' => 'Invalid notes data format'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $liste = $this->noteService->manageNotes($notes);
        $this->noteService->saveNotesInDB($liste);
        //$groupes = $this->noteService->setToFinal($liste->getGroupes()->toArray(), false);

        try {
            return RestResponse::requestResponse('Data received and notes accounted for', 0, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }


    #[Route('/api/recreate-groupe', name: 'api_recreate_groupe', methods: ['GET'])]
    public function reCreateGroups(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $egGrp = $liste->getGroupes();
        $etudiants = [];
        foreach($egGrp as $g){
            $etudiants = array_merge($etudiants, $g->getEtudiant()->toArray());
        }

        $this->groupeService->reDoGrps($liste->getCritere(), 
                        $etudiants, 
                        $egGrp[0]->getTaille(), $liste);

        return RestResponse::requestResponse('List recreated', $liste->getId(), JsonResponse::HTTP_OK);
    }

    //Fonction pour répartir les étudiants dans les groupes souhaités
    #[Route('/api/create-groupe', name: 'api_create_groupe', methods: ['POST'])]
    public function createGroups(Request $request): JsonResponse 
    {
        if (!$request->getContent()) {
            return new JsonResponse(['error' => 'No content'], 400);
        }

        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        //séparation des données du JSON envoyé par le frontend
        $ecole = $data['ecole'] ?? 0;
        $annee = $data['annee'] ?? 0;
        $taille = $data['taille'] ?? 0;
        $nom = $data['nom'] ?? '*Sans nom crée';
        $etudiants = $data['etudiants'] ?? [];
        $criteres = $data['criteres'] ?? [];
        $state = $data['state'] ?? 0;
        $status = $data['status'] ?? 500;

        if($status != 500){
            $newListe = $this->groupeService->manageData($ecole, $annee, $taille, $nom, $etudiants, $criteres, $state);
        }

        try {
            return RestResponse::requestResponse('Data received and list created', $newListe->getId(), JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
        
    }


    #[Route('/api/create-groupe-import', name: 'api_create_groupe_import', methods: ['POST'])]
    public function createImportGroups(Request $request): JsonResponse 
    {
        if (!$request->getContent()) {
            return new JsonResponse(['error' => 'No content'], 400);
        }

        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $ecole = $data['ecole'] ?? 0;
        $annee = $data['annee'] ?? 0;
        $fileName = $data['fileName'] ?? '*Sans nom importé';
        $etudiantGroups = $data['etudiantGroups'] ?? [];

        $nEcole = $this->ecoleRepository->find($ecole);
        $newListe = $this->groupeService->setListe($nEcole, $annee, $fileName, []);
        $newListe->setImported(true);
        $this->listeRepository->addOrUpdate($newListe);
        
        try {
            $this->groupeService->manageImports($etudiantGroups, $newListe, $nEcole);
            return RestResponse::requestResponse('Data received and list created', $newListe->getId(), JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            //$this->cleanWhenError($newListe);
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/api/change-groupe-coach', name: 'api_change_groupe_coach', methods: ['GET'])]
    public function changeCoachGroups(Request $request): JsonResponse 
    {
        $coachId = $request->query->getInt('coach', 0);
        $groupId = $request->query->getInt('groupe', 0);
        $coach = $this->userRepository->find($coachId);
        $group = $this->groupeRepository->find($groupId);

        $group->getCoach()->removeGroupe($group);
        $coach->addGroupe($group);
        $this->entityManager->persist($group);
        $this->entityManager->persist($coach);
        $this->entityManager->flush();

        return RestResponse::requestResponse('Coach has been updated', $group->getId(), JsonResponse::HTTP_OK);
    }

    #[Route('/api/top-100', name: 'api_top_100', methods: ['GET'])]
    public function top100Groups(Request $request): JsonResponse 
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $groupes = $this->groupeRepository->findAllByListe($liste);
        $groupes = $this->groupeService->getTop100GroupesByNote($groupes);
        $results = [];
        foreach($groupes as $grp){
            $dto = (new FinalGroupeResponseDto())->toDto($grp);
            $results[] = [
                'id' => $dto->getId(),
                'libelle' => $dto->getLibelle(),
                'note' => $dto->getNote(),
                'coach' => $dto->getCoach()
            ];
        }

        return RestResponse::linearResponse($results, count($results), JsonResponse::HTTP_OK);
    }

    #[Route('/api/top-2', name: 'api_top_2', methods: ['GET'])]
    public function top2GroupsPerCoach(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $results = [];
        foreach($this->groupeService->getArrayTopNPerCoach($liste, 2) as $grp){
            $dto = (new FinalGroupeResponseDto())->toDto($grp);
            $results[] = [
                'id' => $dto->getId(),
                'libelle' => $dto->getLibelle(),
                'note' => $dto->getNote(),
                'coach' => $dto->getCoach()
            ];
        }

        return RestResponse::linearResponse($results, count($results), JsonResponse::HTTP_OK);
    }

    #[Route('/api/finaliste-export', name: 'api_finaliste_export', methods: ['GET'])]
    public function exportFinalistes(Request $request): BinaryFileResponse
    {
        $mode = $request->query->getString('mode', '100');
        $number = $request->query->getInt('number', 0);
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);
        $excelFile = $this->exportService->makeFinalistesSheet($liste, $mode, $number);

        return new BinaryFileResponse($excelFile);
    }

}
