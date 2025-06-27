<?php

namespace App\Controller;

use App\Controller\Dto\Response\CoachResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Coach;
use App\Entity\Groupe;
use App\Entity\Jury;
use App\Entity\Liste;
use App\Enums\Etat;
use App\Repository\CoachRepository;
use App\Repository\EcoleRepository;
use App\Repository\GroupeRepository;
use App\Repository\JuryRepository;
use App\Repository\ListeRepository;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CoachController extends AbstractController
{
    private $entityManager;
    private $ecoleRepository;
    private $coachRepository;
    private $salleRepository;
    private $listeRepository;
    private $groupeRepository;
    private $juryRepository;

    public function __construct(EntityManagerInterface $entityManager, JuryRepository $juryRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository, SalleRepository $salleRepository, EcoleRepository $ecoleRepository, CoachRepository $coachRepository)
    {
        $this->entityManager = $entityManager;
        $this->ecoleRepository = $ecoleRepository;
        $this->coachRepository = $coachRepository;
        $this->salleRepository = $salleRepository;
        $this->listeRepository = $listeRepository;
        $this->groupeRepository = $groupeRepository;
        $this->juryRepository = $juryRepository;
    }

    #[Route('/api/coach-find', name: 'api_coach_find', methods: ['GET'])]
    public function findCoach(Request $request): JsonResponse
    {
        $coachId = $request->query->getInt('coach', 0);
        $coach = $this->coachRepository->find($coachId);
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

        $coachs = $this->coachRepository->findAllByEcoleUnarchived($liste->getEcole());
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

        $coachs = $this->coachRepository->findAllByEcoleUnarchived($ecole);
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
                'etat' => $r->getEtat(),
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

        if($listeId >0){
            $liste = $this->listeRepository->find($listeId);
            $coachs = $this->coachRepository->findAllPaginatedByEcoleUnarchived($page, $limit, $keyword, $liste->getEcole());
        }else{
            $ecole = $ecoleId>0 ? $this->ecoleRepository->find($ecoleId) : null;
            $coachs = $this->coachRepository->findAllPaginatedByEcoleUnarchived($page, $limit, $keyword, $ecole);
        }
        
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

    #[Route('/api/add-coach', name: 'api_add_coach', methods: ['POST'])]
    public function addCoach(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['coachId'] ?? 0;
        $nom = $data['coach']['nom'] ?? null;
        $prenom = $data['coach']['prenom'] ?? null;
        $tel = $data['coach']['tel'] ?? null;
        $email = $data['coach']['email'] ?? null;
        $ecoleId = $data['coach']['ecole'] ?? 0;
        $option1 = $data['coach']['option1'] ?? null;
        $option2 = $data['coach']['option2'] ?? null;
        $option3 = $data['coach']['option3'] ?? null;

        $ecole = $ecoleId > 0 ? $this->ecoleRepository->find($ecoleId) : null;
        if($tel){
            $coach = $id > 0 ? $this->coachRepository->find($id) : new Coach();
            $coach->setNom($nom)
                    ->setPrenom($prenom)
                    ->setTelephone($tel)
                    ->setEmail($email)
                    ->setEcole($ecole)
                    ->setArchived(false);
            if($option3){
                $coach->setEtat(Etat::Senior);
            }else if($option2){
                $coach->setEtat(Etat::Moyen);
            }else{
                $coach->setEtat(Etat::Debutant);
            }

            $this->coachRepository->addOrUpdate($coach);

            return RestResponse::requestResponse('coach created!', 0, JsonResponse::HTTP_OK);
        }

        return RestResponse::requestResponse('failed to create', $email, JsonResponse::HTTP_BAD_REQUEST);
    }

    #[Route('/api/coach-modif', name: 'api_coach_modif', methods: ['GET'])]
    public function modifCoach(Request $request): JsonResponse
    {
        $coachId = $request->query->getInt('coach', 0);

        $coach = $this->coachRepository->find($coachId);
        $coach->setArchived(true);
        
        $this->coachRepository->addOrUpdate($coach);
        
        return RestResponse::requestResponse('Coach has been updated', 0, JsonResponse::HTTP_OK);
    }

    #[Route('/api/get-coach', name: 'app_coach_get', methods: ['GET'])]
    public function findByListe(Request $request): JsonResponse
    {
        $listeId = $request->query->getInt('liste', 0);
        $liste = $this->listeRepository->find($listeId);

        $coachs = [];
        foreach($liste->getGroupes() as $grp){
            in_array($grp->getCoach(), $coachs, false)? null:$cc=$grp->getCoach();
            $cc ? $coachs[] = $cc : null;
        }
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
        foreach ($coachs as $c) {
            $coach = $this->coachRepository->find($c['id']);
            $coach ? $this->checkCoachInListe($coach, $liste) : null;
            while((isset($groups[$cnt])) && ($cnt < $numGP*($sch + 1)) && ($coach)){
                $cnt++;
                $grp = $groups[$cnt - 1];
                $coach->addGroupe($grp);
                $salles[$sch]->addGroupe($grp);
                $this->groupeRepository->addOrUpdate($grp);
            }

            $this->entityManager->persist($coach);
            $this->entityManager->persist($salles[$sch]);
            $this->entityManager->flush();
            $sch++;
        }

        $this->makeJury($liste, $coachs);

        return RestResponse::requestResponse('Data received and used', 0, JsonResponse::HTTP_OK);
    }

    private function checkCoachInListe(Coach $coach, Liste $liste)
    {
        $filteredGrps = array_filter($coach->getGroupes()->toArray(), function (Groupe $groupe) use ($liste) {
            return $groupe->getListe() === $liste;
            });

        foreach($filteredGrps as $grp){
            $coach->removeGroupe($grp);
            $this->entityManager->persist($grp);
        }
        $this->entityManager->persist($coach);
        $this->entityManager->flush();
    }

    private function makeJury(Liste $liste, array $coaches)
    {
        foreach($liste->getJuries() as $jury){
            $this->entityManager->remove($jury);
        }
        $this->entityManager->flush();
        $this->listeRepository->addOrUpdate($liste);

        $cces = $liste->getEcole()->getCoaches()->toArray();
        $coachsEcole = [];
        foreach($cces as $c){ in_array($c, $coaches, false) ? null : $coachsEcole[]=$c; }
        shuffle($coachsEcole);

        $p = 0;
        while($p < count($coaches)){
            $filteredGrps = array_filter($coaches[$p]->getGroupes()->toArray(), function (Groupe $groupe) use ($liste) {
                return $groupe->getListe() === $liste;
                });

            $jury = new Jury();
            $jury->setArchived(false)
                ->setListe($liste)
                ->setEffectif(2);
            $this->entityManager->persist($jury);
            isset($coaches[$p+1]) ? $jury->addCoach($coaches[$p+1]) : $jury->addCoach($coaches[0]);
            isset($coaches[$p+1]) ? $this->entityManager->persist($coaches[$p+1]) : $this->entityManager->persist($coaches[0]);

            isset($coachsEcole[$p]) ? $jury->addCoach($coachsEcole[$p]) : null;
            isset($coachsEcole[$p]) ? $this->entityManager->persist($coachsEcole[$p]) : null;
            
            foreach($filteredGrps as $grp){
                $jury->addGroupe($grp);
                $jury->setLibelle('Jury '. $p+1 .' - ' . $grp->getSalle()->getLibelle());
                $this->entityManager->persist($grp);
            }
            $this->entityManager->persist($jury);
            $this->entityManager->flush();

            $p++;
        }
    }

}
