<?php

namespace App\Controller;

use App\Controller\Dto\Request\CoachRequestDto;
use App\Controller\Dto\Response\CoachResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Coach;
use App\Entity\Groupe;
use App\Entity\Jury;
use App\Entity\Liste;
use App\Enums\Etat;
use App\Enums\Role;
use App\Repository\CoachRepository;
use App\Repository\EcoleRepository;
use App\Repository\GroupeRepository;
use App\Repository\JuryRepository;
use App\Repository\ListeRepository;
use App\Repository\SalleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CoachService;

class CoachController extends AbstractController
{
    private $entityManager;
    private $ecoleRepository;
    private $userRepository;
    private $salleRepository;
    private $listeRepository;
    private $groupeRepository;
    private $juryRepository;
    private $coachService;

    public function __construct(EntityManagerInterface $entityManager, CoachService $coachService, JuryRepository $juryRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository, SalleRepository $salleRepository, EcoleRepository $ecoleRepository, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->ecoleRepository = $ecoleRepository;
        $this->userRepository = $userRepository;
        $this->salleRepository = $salleRepository;
        $this->listeRepository = $listeRepository;
        $this->groupeRepository = $groupeRepository;
        $this->juryRepository = $juryRepository;
        $this->coachService = $coachService;
    }

    #[Route('/api/coach-find', name: 'api_coach_find', methods: ['GET'])]
    public function findCoach(Request $request): JsonResponse
    {
        $coachId = $request->query->getInt('coach', 0);
        $coach = $this->userRepository->find($coachId);
        if($coach != null){ 
            $dto = (new CoachResponseDto())->toDto($coach); 
            $dto = [
                    'id' => $dto->getId(),
                    'nom' => $dto->getNom(),
                    'prenom' => $dto->getPrenom(),
                    'tel' => $dto->getTel(),
                    'email' => $dto->getEmail(),
                    'etat' => $dto->getEtat(),
                    'ecole' => $dto->getEcole(),
                    'ecoleId' => $dto->getEcoleId()
                ];
        }

        return RestResponse::linearResponse($dto, 1, JsonResponse::HTTP_OK);
    }

    #[Route('/api/coach-find-unaffected', name: 'api_coach_find_unaffected', methods: ['GET'])]
    public function findCoachNotAffected(Request $request): JsonResponse
    {
        $final = $request->query->getInt('final', 0);
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);

        $coachsListe = [];
        if($final == 0){
            foreach($liste->getJuries() as $jury){
                $coachsListe = array_merge($coachsListe, $jury->getCoaches()->toArray());
            }
        }else{
            $jury = $this->juryRepository->findFinalistJuryByList($liste);
            $coachsListe = $jury->getCoaches()->toArray();
        }

        $coachs = $this->userRepository->findAllByEcoleOrRole($liste->getEcole(), Role::COACH->value);
        $coachLeft=[];
        foreach($coachs as $coach){
            in_array($coach, $coachsListe, false) ? null : $coachLeft[] = $coach;
        }

        $dtos=[];
        foreach ($coachLeft as $coach){
            $dtos[] = (new CoachResponseDto())->toDto($coach);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'nom' => $r->getNom(),
                'prenom' => $r->getPrenom(),
                'tel' => $r->getTel(),
                'email' => $r->getEmail(),
                'etat' => $r->getEtat(),
                'ecole' => $r->getEcole()
            ];
        }

        $totalItems = count($coachLeft);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/all-coach', name: 'app_coach_all', methods: ['GET'])]
    public function findAllByEcole(Request $request): JsonResponse
    {
        $ecoleId = $request->query->getInt('ecole', 0);
        $ecole = $ecoleId>0 ? $this->ecoleRepository->find($ecoleId) : null;

        $coachs = $this->userRepository->findAllByEcoleOrRole($ecole, Role::COACH->value);
        $dtos=[];
        foreach ($coachs as $coach){
            $dtos[] = (new CoachResponseDto())->toDto($coach);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'nom' => $r->getNom(),
                'prenom' => $r->getPrenom(),
                'tel' => $r->getTel(),
                'email' => $r->getEmail(),
                //'etat' => $r->getEtat(),
                'ecole' => $r->getEcole()
            ];
        }

        $totalItems = count($coachs);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/liste-coach', name: 'app_coach_liste', methods: ['GET'])]
    public function listerCoachs(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $keyword = $request->query->getString('keyword', '');
        $ecoleId = $request->query->getInt('ecole', 0);
        $listeId = $request->query->getInt('liste', 0);

        $liste = $listeId >0? $this->listeRepository->find($listeId) : null;
        $coachs = $this->userRepository->findAllPaginated($page, $limit, $keyword, Role::COACH->value, $ecoleId);
        
        $dtos = [];
        foreach ($coachs as $coach) {
            $dtos[] = (new CoachResponseDto())->toDto($coach);
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'nom' => $r->getNom(),
                'prenom' => $r->getPrenom(),
                'tel' => $r->getTel(),
                'email' => $r->getEmail(),
                'etat' => $r->getEtat(),
                'ecole' => $r->getEcole()
            ];
        }
        

        $totalItems = $coachs->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    // #[Route('/api/add-coach', name: 'api_add_coach', methods: ['POST'])]
    // public function addCoach(Request $request): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $id = $data['coachId'] ?? 0;
    //     $nom = $data['coach']['nom'] ?? null;
    //     $prenom = $data['coach']['prenom'] ?? null;
    //     $tel = $data['coach']['tel'] ?? null;
    //     $email = $data['coach']['email'] ?? null;
    //     $ecoleId = $data['coach']['ecole'] ?? 0;
    //     $option1 = $data['coach']['option1'] ?? null;
    //     $option2 = $data['coach']['option2'] ?? null;
    //     $option3 = $data['coach']['option3'] ?? null;

    //     $ecole = $ecoleId > 0 ? $this->ecoleRepository->find($ecoleId) : null;
    //     if($tel){
    //         $coach = $id > 0 ? $this->coachRepository->find($id) : new Coach();
    //         $coach->setNom($nom)
    //                 ->setPrenom($prenom)
    //                 ->setTelephone($tel)
    //                 ->setEmail($email)
    //                 ->setEcole($ecole)
    //                 ->setArchived(false);
    //         if($option3){
    //             $coach->setEtat(Etat::Senior);
    //         }else if($option2){
    //             $coach->setEtat(Etat::Moyen);
    //         }else{
    //             $coach->setEtat(Etat::Debutant);
    //         }

    //         $this->coachRepository->addOrUpdate($coach);

    //         return RestResponse::requestResponse('coach created!', 0, JsonResponse::HTTP_OK);
    //     }

    //     return RestResponse::requestResponse('failed to create', $email, JsonResponse::HTTP_BAD_REQUEST);
    // }

    // #[Route('/api/coach-modif', name: 'api_coach_modif', methods: ['GET'])]
    // public function modifCoach(Request $request): JsonResponse
    // {
    //     $coachId = $request->query->getInt('coach', 0);

    //     $coach = $this->coachRepository->find($coachId);
    //     $coach->setArchived(true);
        
    //     $this->coachRepository->addOrUpdate($coach);
        
    //     return RestResponse::requestResponse('Coach has been updated', 0, JsonResponse::HTTP_OK);
    // }

    #[Route('/api/get-coach', name: 'app_coach_get', methods: ['GET'])]
    public function findByListe(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);

        $coachs = [];
        foreach($liste->getGroupes() as $grp){
            $cc=$grp->getCoach();
            in_array($cc, $coachs, true)? null:$coachs[] = $cc;
        }
        $dtos = [];
        foreach ($coachs as $coach) {
            $coach ? $dtos[] = (new CoachResponseDto())->toDto($coach) : null;
        }
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'nom' => $r->getNom(),
                'prenom' => $r->getPrenom(),
                'tel' => $r->getTel(),
                'email' => $r->getEmail(),
                'etat' => $r->getEtat(),
                'ecole' => $r->getEcole()
            ];
        }

        $totalItems = count($coachs);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

    #[Route('/api/assign-coach', name: 'api_assign_coach', methods: ['POST'])]
    public function assignCoach(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $coachs = $data['coachs'] ?? [];
        $salles = $data['salles'] ?? [];
        $listeId = $data['liste'] ?? 0;

        $liste = $this->listeRepository->find($listeId);
        $groups = $this->groupeRepository->findAllByListe($liste);

        count($coachs) > 0 ? $numGP = intdiv(count($groups) + count($coachs) - 1, count($coachs)) : null;
        if(count($coachs) > count($salles)){
            return RestResponse::requestResponse('More coaches than salles', 1, JsonResponse::HTTP_OK);
        }

        $sch = 0;
        $cnt = 0;
        $coachs = $this->coachService->convertJsonDataToCoachArray($coachs);
        foreach ($coachs as $coach) {
            $this->coachService->checkCoachInListe($coach, $liste);
            $coach->addListe($liste);
            while((isset($groups[$cnt])) && ($cnt < $numGP*($sch + 1))){
                $cnt++;
                $grp = $groups[$cnt - 1];
                $coach->addGroupe($grp);
                $salle = $this->salleRepository->find($salles[$sch]['id']);
                $salle ? $salle->addGroupe($grp) : null;
                $this->groupeRepository->addOrUpdate($grp);
            }

            $this->entityManager->persist($coach);
            $this->entityManager->persist($salle);
            $this->entityManager->flush();
            $sch++;
        }

        $this->coachService->makeJury($liste, $coachs);

        return RestResponse::requestResponse('Data received and used', 0, JsonResponse::HTTP_OK);
    }

    #[Route('/api/except-one-coach', name: 'app_coach_except_one', methods: ['GET'])]
    public function findAllExceptOne(Request $request): JsonResponse
    {
        $groupeId = $request->query->getInt('groupe', 0);
        $groupe = $this->groupeRepository->find($groupeId);
        $coachs = $this->userRepository->findAllCoachExceptOneById($groupe->getCoach()->getId());

        $dtos = [];
        foreach ($coachs as $coach) {
            $coach ? $dtos[] = (new CoachRequestDto())->toDto($coach) : null;
        }

        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'nom' => $r->getNom(),
                'prenom' => $r->getPrenom()
            ];
        }
        $totalItems = count($coachs);

        return RestResponse::linearResponse($results, $totalItems, JsonResponse::HTTP_OK);
    }

}
