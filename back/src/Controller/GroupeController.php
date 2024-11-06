<?php

namespace App\Controller;

use App\Controller\Dto\Request\EtudiantRequestDto;
use App\Controller\Dto\Request\GroupeRequestDto;
use App\Controller\Dto\Response\EtudiantResponseDto;
use App\Controller\Dto\Response\GroupeResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Classe;
use App\Entity\Ecole;
use App\Entity\Etudiant;
use App\Entity\Filiere;
use App\Entity\Groupe;
use App\Entity\Liste;
use App\Entity\Niveau;
use App\Repository\AbsenceRepository;
use App\Repository\AnneeRepository;
use App\Repository\ClasseRepository;
use App\Repository\EcoleRepository;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use App\Repository\GroupeRepository;
use App\Repository\JourRepository;
use App\Repository\ListeRepository;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use PhpParser\Node\Expr\Cast\Array_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GroupeController extends AbstractController
{
    private $ecoleRepository;
    private $anneeRepository;
    private $etudiantRepository;
    private $niveauRepository;
    private $filiereRepository;
    private $classeRepository;
    private $groupeRepository;
    private $listeRepository;
    private $entityManager;
    private $jourRepository;
    private $absenceRepository;

    public function __construct(EntityManagerInterface $entityManager, AbsenceRepository $absenceRepository, JourRepository $jourRepository, EcoleRepository $ecoleRepository, AnneeRepository $anneeRepository, EtudiantRepository $etudiantRepository, NiveauRepository $niveauRepository, FiliereRepository $filiereRepository, ClasseRepository $classeRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->entityManager = $entityManager;
        $this->ecoleRepository = $ecoleRepository;
        $this->anneeRepository = $anneeRepository;
        $this->etudiantRepository = $etudiantRepository;
        $this->niveauRepository = $niveauRepository;
        $this->filiereRepository = $filiereRepository;
        $this->classeRepository = $classeRepository;
        $this->groupeRepository = $groupeRepository;
        $this->listeRepository = $listeRepository;
        $this->jourRepository = $jourRepository;
        $this->absenceRepository = $absenceRepository;
    }

    #[Route('/api/all-groupe', name: 'app_all_groupe', methods: ['GET'])]
    public function listerAllGroupe(Request $request): JsonResponse
    {
        $liste = $request->query->getInt('liste', 0);
        $groupes = $this->groupeRepository->findAllByListe($this->listeRepository->find($liste));
        $dtos = [];
        foreach ($groupes as $g) {
            $dtos[] = (new GroupeRequestDto())->toDto($g);
        } 
        $results = [];
        foreach ($dtos as $r) {
            $results[] = [
                'id' => $r->getId(),
                'libelle' => $r->getLibelle()
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

        $jour = $this->jourRepository->find($jourId);
        $groupes = $this->groupeRepository->findAllByListePaginated($page, $limit, $jour->getListe(), $this->groupeRepository->find($groupeId));

        $results = $this->grpJourDto($groupes, $jour);
        $totalItems = $groupes->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }
    private function grpJourDto($groups, $jour):array
    {
        $dtos = [];
        foreach($groups as $g){
            $eds = [];
            foreach($g->getEtudiant() as $e){
                $eds[] = (new EtudiantRequestDto())->toDto($e, $this->absenceRepository->findAllByJourAndEtudiant($jour, $e));
            }
            $etds = [];
            foreach($eds as $e){
                $etds[] = [
                    'id' => $e->getId(),
                    'matricule' => $e->getMatricule(),
                    'nom' => $e->getNom(),
                    'prenom' => $e->getPrenom(),
                    'classe' => $e->getClasse(),
                    'groupe' => $e->getGroupe(),
                    'emargement1' => $e->getEmargement1(),
                    'emargement2' => $e->getEmargement2()
                ];
            }

            $dtos[] = (new GroupeResponseDto())->toDto($g, $etds);
        }

        $results = [];
        foreach($dtos as $d){
            $results[] = [
                'id' => $d->getId(),
                'libelle' => $d->getLibelle(),
                'liste' => $d->getListe(),
                'listeT' => $d->getListeT(),
                'etudiants' => $d->getEtudiants(),
                'note' => $d->getNote()
            ];
        }

        return $results;
    }


    #[Route('/api/liste-groupe', name: 'api_groupe_liste', methods: ['GET'])]
    public function listeGroupe(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 0);
        $limit = $request->query->getInt('limit', 10);
        $liste = $request->query->getInt('liste', 0);
        $groupes = $this->groupeRepository->findAllByListePaginated($page, $limit, $this->listeRepository->find($liste));

        $results = $this->grpListeDto($groupes);
        $totalItems = $groupes->count();
        $totalPages = ceil($totalItems / $limit);

        return RestResponse::paginateResponse($results, $page, $totalItems, $totalPages, JsonResponse::HTTP_OK);
    }

    private function grpListeDto($groups):array
    {
        $dtos = [];
        foreach($groups as $g){
            $eds = [];
            foreach($g->getEtudiant() as $e){
                $eds[] = (new EtudiantResponseDto())->toDto($e);
            }
            $etds = [];
            foreach($eds as $e){
                $etds[] = [
                    'id' => $e->getId(),
                    'matricule' => $e->getMatricule(),
                    'nom' => $e->getNom(),
                    'prenom' => $e->getPrenom(),
                    'sexe' => $e->getSexe(),
                    'nationalite' => $e->getNationalite(),
                    'classe' => $e->getClasse(),
                    'niveau' => $e->getNiveau(),
                    'filiere' => $e->getFiliere(),
                    'groupe' => $e->getGroupe(),
                    'noteEtd' => $e->getNoteEtd(),
                    'noteFinal' => $e->getNoteFinal()
                ];
            }

            $dtos[] = (new GroupeResponseDto())->toDto($g, $etds);
        }

        $results = [];
        foreach($dtos as $d){
            $results[] = [
                'id' => $d->getId(),
                'libelle' => $d->getLibelle(),
                'liste' => $d->getListe(),
                'listeT' => $d->getListeT(),
                'etudiants' => $d->getEtudiants(),
                'note' => $d->getNote()
            ];
        }

        return $results;
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
        $this->manageNotes($notes);

        try {
            return RestResponse::requestResponse('Data received and notes accounted for', 0, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function manageNotes(array $notes)
    {
        $grp = $this->groupeRepository->find($notes[0]['id']);
        $pts = $this->marksPerPeriod($grp->getListe());
        foreach($notes as $note){
            $groupe = $this->groupeRepository->find($note['id']);
            $groupe->setNote($this->checkNote((float) $note['note']));
            foreach($groupe->getEtudiant() as $etd){
                $noteEtd = (float) $note['note'];
                foreach($etd->getAbsences() as $abs){
                    $noteEtd = $noteEtd - $pts;
                }
                $etd->setNoteEtd($this->checkNote($noteEtd));
                $final = (((float) $note['note']) + ($this->checkNote($noteEtd)))/2.0;
                $etd->setNoteFinal($this->checkNote($final));
                $this->etudiantRepository->addOrUpdate($etd);
            }
            $this->groupeRepository->addOrUpdate($groupe);
        }
    }
    private function marksPerPeriod(Liste $liste): float
    {
        $periods = ($liste->getJours()->count())*2;
        return  20.0/$periods;
    }
    private function checkNote(float $note):float
    {
        if($note < 0){
            return 0.0;
        }
        return $note;
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

        $this->reDoGrps($liste->getCritere(), 
                        $etudiants, 
                        $egGrp[0]->getTaille(), $liste);

        return RestResponse::requestResponse('List recreated', $liste->getId(), JsonResponse::HTTP_OK);
    }

    private function reDoGrps($criteres, $etudiants, $taille, Liste $liste)
    {
        $etds = $etudiants;
        $tlaEtds = count($etds);

        shuffle($etds);
        $num = 1;
        while ($tlaEtds > 0){
            foreach ($liste->getGroupes()->toArray() as $g) {
                $t = 0;
                $group = [];
                $this->clearEtds($g);

                foreach ($criteres['filiere'] as $crit) {
                    if(isset($crit['choix']) || trim($crit['choix']) != ''){
                        if($crit['choix'] == -1){
                            $fnum = 0;
                            while((count($group) < $taille) && (count($etds) > 0)){
                                if(!$this->checkLoad($group, 'filiere', $etds[$fnum]->getClasse()->getFiliere(), 1)){
                                    $group[] = $etds[$fnum];
                                    $g->addEtudiant($group[count($group) - 1]);
                                    unset($etds[$fnum]);
                                    $etds = array_values($etds);
                                    $fnum--;
                                }
                                $fnum++;
                            }
                        }else{
                            $tailleLeft = $this->checkTaille($crit['taille']);
                            $filiere = $this->filiereRepository->find($crit['choix']);
                            $filteredEtds = array_filter($etds, function (Etudiant $etudiant) use ($filiere) {
                                            return $etudiant->getClasse()->getFiliere() === $filiere;
                                            });
                            if((count($crit['niveau'])<1)){
                                $etdToAdd = min($tailleLeft, count($filteredEtds));
                                for ($i = 0; $i < $etdToAdd; $i++) {
                                    if((count($group) < $taille)){
                                        $group[] = array_shift($filteredEtds);
                                        $g->addEtudiant($group[count($group) - 1]);
                                        unset($etds[array_search($group[count($group) - 1], $etds)]);
                                        $etds = array_values($etds);
                                    }
                                    
                                }
                            }else{
                                foreach($crit['niveau'] as $niv){
                                    if(isset($niv['choix']) || trim($niv['choix']) != ''){
                                        $niveau = $this->niveauRepository->find($niv['choix']);
                                        $filteredEtds = array_filter($filteredEtds, function (Etudiant $etudiant) use ($niveau) {
                                                        return $etudiant->getClasse()->getNiveau() === $niveau;
                                                        });
                                        $miniTaille = $this->checkTaille($niv['taille']);
                                        $etdToAdd = min($miniTaille, count($filteredEtds));
                                        $tailleLeft -= $etdToAdd;
                                        for ($i = 0; $i < $etdToAdd; $i++) {
                                            if((count($group) < $taille)){
                                                $group[] = array_shift($filteredEtds);
                                                $g->addEtudiant($group[count($group) - 1]);
                                                unset($etds[array_search($group[count($group) - 1], $etds)]);
                                                $etds = array_values($etds);
                                            }
                                            
                                        }
                                    }
                                }
                                $filteredEtds = array_filter($etds, function (Etudiant $etudiant) use ($filiere) {
                                                return $etudiant->getClasse()->getFiliere() === $filiere;
                                                });
                                $etdToAdd = min($tailleLeft, count($filteredEtds));
                                for ($i = 0; $i < $etdToAdd; $i++) {
                                    if((count($group) < $taille)){
                                        $group[] = array_shift($filteredEtds);
                                        $g->addEtudiant($group[count($group) - 1]);
                                        unset($etds[array_search($group[count($group) - 1], $etds)]);
                                        $etds = array_values($etds);
                                    }  
                                }
                            }
                        }
                        
                    }
                }
    
                foreach ($criteres['classe'] as $crit) {
                    if(isset($crit['choix']) || trim($crit['choix']) != ''){
                        if($crit['choix'] == -1){
                            $fnum = 0;
                            while((count($group) < $taille) && (count($etds) > 0)){
                                if(!$this->checkLoad($group, 'classe', $etds[$fnum]->getClasse(), 1)){
                                    $group[] = $etds[$fnum];
                                    $g->addEtudiant($group[count($group) - 1]);
                                    unset($etds[array_search($group[count($group) - 1], $etds)]);
                                    $etds = array_values($etds);
                                    $fnum--;
                                }
                                $fnum++;
                            }
                        }else{
                            $tailleLeft = $this->checkTaille($crit['taille']);
                            $classe = $this->classeRepository->find($crit['choix']);
                            $filteredEtds = array_filter($etds, function (Etudiant $etudiant) use ($classe) {
                                            return $etudiant->getClasse() === $classe;
                                            });
                            $etdToAdd = min($tailleLeft, count($filteredEtds));
                            for ($i = 0; $i < $etdToAdd; $i++) {
                                if((count($group) < $taille)){
                                    $group[] = array_shift($filteredEtds);
                                    $g->addEtudiant($group[count($group) - 1]);
                                    unset($etds[array_search($group[count($group) - 1], $etds)]);
                                    $etds = array_values($etds);
                                }
                                
                            }
                        }
                    
                    }
                }
    
                foreach ($criteres['niveau'] as $crit) {
                    if(isset($crit['choix']) || trim($crit['choix']) != ''){
                        if($crit['choix'] == -1){
                            $fnum = 0;
                            while((count($group) < $taille) && (count($etds) > 0)){
                                if(!$this->checkLoad($group, 'niveau', $etds[$fnum]->getClasse()->getNiveau(), 1)){
                                    $group[] = $etds[$fnum];
                                    $g->addEtudiant($group[count($group) - 1]);
                                    unset($etds[array_search($group[count($group) - 1], $etds)]);
                                    $etds = array_values($etds);
                                    $fnum--;
                                }
                                $fnum++;
                            }
                        }else{
                            $tailleLeft = $this->checkTaille($crit['taille']);
                            $niveau = $this->niveauRepository->find($crit['choix']);
                            $filteredEtds = array_filter($etds, function (Etudiant $etudiant) use ($niveau) {
                                            return $etudiant->getClasse()->getNiveau() === $niveau;
                                            });
                            $etdToAdd = min($tailleLeft, count($filteredEtds));
                            for ($i = 0; $i < $etdToAdd; $i++) {
                                if((count($group) < $taille)){
                                    $group[] = array_shift($filteredEtds);
                                    $g->addEtudiant($group[count($group) - 1]);
                                    unset($etds[array_search($group[count($group) - 1], $etds)]);
                                    $etds = array_values($etds);
                                }
                                
                            }
                        }
                        
                    }
                }
    
                while((count($group) < $taille) && (count($etds) > 0)){
                    if (!$this->checkFinal($group, $etds[$t], $criteres)){
                        $group[] = $etds[$t];
                        $g->addEtudiant($etds[$t]);
                        unset($etds[$t]);
                        $etds = array_values($etds);
                        $t = -1;
                    }
                    $t++;
                }
                
                $g->setTaille(count($g->getEtudiant()));
                $this->groupeRepository->addOrUpdate($g);
                $tlaEtds -= count($g->getEtudiant());
                $num++; 
            }
        }
    }

    public function clearEtds(Groupe $groupe): void
    {
        $etudiants = $groupe->getEtudiant();

        foreach ($etudiants as $etudiant) {
            $groupe->removeEtudiant($etudiant); 
        }
        $this->entityManager->flush();
    }

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

        $ecole = $data['ecole'] ?? 0;
        $annee = $data['annee'] ?? 0;
        $taille = $data['taille'] ?? 0;
        $nom = $data['nom'] ?? '';
        $etudiants = $data['etudiants'] ?? [];
        $criteres = $data['criteres'] ?? [];
        $status = $data['status'] ?? 500;

        if($status != 500){
            $newListe = $this->manageData($ecole, $annee, $taille, $nom, $etudiants, $criteres);
        }

        try {
            return RestResponse::requestResponse('Data received and list created', $newListe->getId(), JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
        
    }

    private function manageData($ecoleId, $annee, $taille, $nom, $etudiants, $criteres): ?Liste
    {
        $ecole = $this->ecoleRepository->find($ecoleId);
        $this->setClasses($etudiants, $ecole);
        $newListe = $this->setListe($ecole, $annee, $nom, $criteres);
        $newEtds = $this->loadEtudiants($etudiants, $ecole);
        $this->loadGroups($criteres, $newEtds, $taille, $newListe);
        
        return $newListe;
    }

    private function loadGroups($criteres, $etudiants, $taille, $liste): void
    {
        $etds = $etudiants;
        $tlaEtds = count($etds);

        shuffle($etds);
        $num = 1;
        while ($tlaEtds > 0){
            $t = 0;
            $group = [];
            $newGrp = new Groupe();
            $newGrp->setArchived(false)
                    ->setListe($liste)
                    ->setLibelle('Groupe '.$num);

            foreach ($criteres['filiere'] as $crit) {
                if(isset($crit['choix']) || trim($crit['choix']) != ''){
                    if($crit['choix'] == -1){
                        $fnum = 0;
                        while((count($group) < $taille) && (count($etds) > 0)){
                            if(!$this->checkLoad($group, 'filiere', $etds[$fnum]->getClasse()->getFiliere(), 1)){
                                $group[] = $etds[$fnum];
                                $newGrp->addEtudiant($group[count($group) - 1]);
                                unset($etds[$fnum]);
                                $etds = array_values($etds);
                                $fnum--;
                            }
                            $fnum++;
                        }
                    }else{
                        $tailleLeft = $this->checkTaille($crit['taille']);
                        $filiere = $this->filiereRepository->find($crit['choix']);
                        $filteredEtds = array_filter($etds, function (Etudiant $etudiant) use ($filiere) {
                                        return $etudiant->getClasse()->getFiliere() === $filiere;
                                        });
                        if((count($crit['niveau'])<1)){
                            $etdToAdd = min($tailleLeft, count($filteredEtds));
                            for ($i = 0; $i < $etdToAdd; $i++) {
                                if((count($group) < $taille)){
                                    $group[] = array_shift($filteredEtds);
                                    $newGrp->addEtudiant($group[count($group) - 1]);
                                    unset($etds[array_search($group[count($group) - 1], $etds)]);
                                    $etds = array_values($etds);
                                }
                                
                            }
                        }else{
                            foreach($crit['niveau'] as $niv){
                                if(isset($niv['choix']) || trim($niv['choix']) != ''){
                                    $niveau = $this->niveauRepository->find($niv['choix']);
                                    $filteredEtds = array_filter($filteredEtds, function (Etudiant $etudiant) use ($niveau) {
                                                    return $etudiant->getClasse()->getNiveau() === $niveau;
                                                    });
                                    $miniTaille = $this->checkTaille($niv['taille']);
                                    $etdToAdd = min($miniTaille, count($filteredEtds));
                                    $tailleLeft -= $etdToAdd;
                                    for ($i = 0; $i < $etdToAdd; $i++) {
                                        if((count($group) < $taille)){
                                            $group[] = array_shift($filteredEtds);
                                            $newGrp->addEtudiant($group[count($group) - 1]);
                                            unset($etds[array_search($group[count($group) - 1], $etds)]);
                                            $etds = array_values($etds);
                                        }
                                        
                                    }
                                }
                            }
                            $filteredEtds = array_filter($etds, function (Etudiant $etudiant) use ($filiere) {
                                            return $etudiant->getClasse()->getFiliere() === $filiere;
                                            });
                            $etdToAdd = min($tailleLeft, count($filteredEtds));
                            for ($i = 0; $i < $etdToAdd; $i++) {
                                if((count($group) < $taille)){
                                    $group[] = array_shift($filteredEtds);
                                    $newGrp->addEtudiant($group[count($group) - 1]);
                                    unset($etds[array_search($group[count($group) - 1], $etds)]);
                                    $etds = array_values($etds);
                                }  
                            }
                        }
                    }
                    
                }
            }

            foreach ($criteres['classe'] as $crit) {
                if(isset($crit['choix']) || trim($crit['choix']) != ''){
                    if($crit['choix'] == -1){
                        $fnum = 0;
                        while((count($group) < $taille) && (count($etds) > 0)){
                            if(!$this->checkLoad($group, 'classe', $etds[$fnum]->getClasse(), 1)){
                                $group[] = $etds[$fnum];
                                $newGrp->addEtudiant($group[count($group) - 1]);
                                unset($etds[$fnum]);
                                $etds = array_values($etds);
                                $fnum--;
                            }
                            $fnum++;
                        }
                    }else{
                        $tailleLeft = $this->checkTaille($crit['taille']);
                        $classe = $this->classeRepository->find($crit['choix']);
                        $filteredEtds = array_filter($etds, function (Etudiant $etudiant) use ($classe) {
                                        return $etudiant->getClasse() === $classe;
                                        });
                        $etdToAdd = min($tailleLeft, count($filteredEtds));
                        for ($i = 0; $i < $etdToAdd; $i++) {
                            if((count($group) < $taille)){
                                $group[] = array_shift($filteredEtds);
                                $newGrp->addEtudiant($group[count($group) - 1]);
                                unset($etds[array_search($group[count($group) - 1], $etds)]);
                                $etds = array_values($etds);
                            }
                            
                        }
                    }
                
                }
            }

            foreach ($criteres['niveau'] as $crit) {
                if(isset($crit['choix']) || trim($crit['choix']) != ''){
                    if($crit['choix'] == -1){
                        $fnum = 0;
                        while((count($group) < $taille) && (count($etds) > 0)){
                            if(!$this->checkLoad($group, 'niveau', $etds[$fnum]->getClasse()->getNiveau(), 1)){
                                $group[] = $etds[$fnum];
                                $newGrp->addEtudiant($group[count($group) - 1]);
                                unset($etds[$fnum]);
                                $etds = array_values($etds);
                                $fnum--;
                            }
                            $fnum++;
                        }
                    }else{
                        $tailleLeft = $this->checkTaille($crit['taille']);
                        $niveau = $this->niveauRepository->find($crit['choix']);
                        $filteredEtds = array_filter($etds, function (Etudiant $etudiant) use ($niveau) {
                                        return $etudiant->getClasse()->getNiveau() === $niveau;
                                        });
                        $etdToAdd = min($tailleLeft, count($filteredEtds));
                        for ($i = 0; $i < $etdToAdd; $i++) {
                            if((count($group) < $taille)){
                                $group[] = array_shift($filteredEtds);
                                $newGrp->addEtudiant($group[count($group) - 1]);
                                unset($etds[array_search($group[count($group) - 1], $etds)]);
                                $etds = array_values($etds);
                            }
                            
                        }
                    }
                    
                }
            }

            while((count($group) < $taille) && (count($etds) > 0)){
                if (!$this->checkFinal($group, $etds[$t], $criteres)){
                    $group[] = $etds[$t];
                    $newGrp->addEtudiant($etds[$t]);
                    unset($etds[$t]);
                    $etds = array_values($etds);
                    $t = -1;
                }
                $t++;
            }
            
            $newGrp->setTaille(count($newGrp->getEtudiant()));
            $this->entityManager->persist($newGrp);
            $tlaEtds -= count($newGrp->getEtudiant());

            if($num % 10 === 0){
                $this->entityManager->flush();
            }
            $num++; 
        }
        $this->entityManager->flush();
    }

    private function checkTaille($givenTaille):int
    {
        if(!isset($givenTaille) || trim($givenTaille) === ''){
            return 1;
        }
        return intval($givenTaille);
    }

    private function checkFinal($group, Etudiant $etudiant, $criteres):bool
    {
        foreach ($criteres['niveau'] as $crit) {
            $i=0;
            $niveau = $this->niveauRepository->find($crit['choix']);
            foreach($group as $etd){
                if(($etd->getClasse()->getNiveau() == $etudiant->getClasse()->getNiveau()) && ($etudiant->getClasse()->getNiveau() == $niveau)){
                    $i++;
                }
                if($i === $crit['taille']){
                    return true;
                }
            }
        }
        
        foreach ($criteres['filiere'] as $crit) {
            $i=0;
            $filiere = $this->filiereRepository->find($crit['choix']);
            foreach($group as $etd){
                if(($etd->getClasse()->getFiliere() == $etudiant->getClasse()->getFiliere()) && ($etudiant->getClasse()->getFiliere() == $filiere)){
                    $i++;
                }
                if($i === $crit['taille']){
                    return true;
                }
            }
        }

        foreach ($criteres['classe'] as $crit) {
            $i=0;
            $classe = $this->classeRepository->find($crit['choix']);
            foreach($group as $etd){
                if(($etd->getClasse() == $etudiant->getClasse()) && ($etudiant->getClasse() == $classe)){
                    $i++;
                }
                if($i === $crit['taille']){
                    return true;
                }
            }
        }    

        return false;
    }

    private function checkLoad($group, $property, $propertyElt, $propertyMax): bool
    {   
        $i = 0;
        switch ($property) {
            case 'niveau':
                foreach($group as $etd){
                    if($etd->getClasse()->getNiveau() == $propertyElt){
                        $i++;
                    }
                    if($i === $propertyMax){
                        return true;
                    }
                }
                break;
            
            case 'filiere':
                foreach($group as $etd){
                    if($etd->getClasse()->getFiliere() == $propertyElt){
                        $i++;
                    }
                    if($i === $propertyMax){
                        return true;
                    }
                }
                break;
            
            case 'classe':
                foreach($group as $etd){
                    if($etd->getClasse() == $propertyElt){
                        $i++;
                    }
                    if($i === $propertyMax){
                        return true;
                    }
                }
                break;
            
            default:
                return false;
                break;
        }

        return false;
    }

    private function loadEtudiants($etudiants, Ecole $ecole): array
    {
        $existingClasses = $this->classeRepository->findAllByEcole($ecole);
        $classesMap = [];
        foreach ($existingClasses as $classe) {
            $classesMap[$classe->getLibelle()] = $classe;
        }

        $newEtudiants = [];
        $batchSize = 150;
        $i = 1;
        foreach ($etudiants as $key => $etudiant) {
            $newEtd = new Etudiant();
            $newEtd->setArchived(false);

            $theEtd = $this->etudiantRepository->findByMatricule($etudiant['Matricule']);
            if($theEtd){                
                $newEtd->setMatricule($theEtd->getMatricule())
                    ->setNom($theEtd->getNom())
                    ->setPrenom($theEtd->getPrenom())
                    ->setSexe($theEtd->getSexe())
                    ->setNationalite($theEtd->getNationalite())
                    ->setClasse($theEtd->getClasse());
            }else{
                $newEtd->setMatricule($etudiant['Matricule'])
                    ->setNom($etudiant['Nom'])
                    ->setPrenom($etudiant['Prenom'])
                    ->setSexe($etudiant['Sexe'])
                    ->setNationalite($etudiant['Nationalite'])
                    ->setClasse($classesMap[$etudiant['Classe']]);
            }

            $this->entityManager->persist($newEtd);
            $newEtudiants[$key] = $newEtd;

            if (($i % $batchSize) === 0) {
                $this->entityManager->flush();
            }
            $i++;
        }
        $this->entityManager->flush();

        return $newEtudiants;
    }

    private function setClasses($etudiants, Ecole $ecole): void
    {
        $existingClasses = $this->classeRepository->findAllByEcole($ecole);
        $existingFilieres = $this->filiereRepository->findAllByEcole($ecole);
        $existingNiveaux = $this->niveauRepository->findAll();

        $classesMap = [];
        foreach ($existingClasses as $classe) {
            $classesMap[$classe->getLibelle()] = $classe;
        }

        $niveauxMap = [];
        foreach ($existingNiveaux as $niveau) {
            $niveauxMap[$niveau->getLibelle()] = $niveau;
        }

        $filieresMap = [];
        foreach ($existingFilieres as $filiere) {
            $filieresMap[$filiere->getLibelle()] = $filiere;
        }

        foreach ($etudiants as $etd) {
            if (!isset($classesMap[$etd['Classe']])) {
                $newClasse = new Classe();
                $newClasse->setArchived(false);
                $newClasse->setLibelle($etd['Classe']);

                if (!isset($niveauxMap[$etd['Niveau']])) {
                    $niveau = new Niveau();
                    $niveau->setArchived(false);
                    $niveau->setLibelle($etd['Niveau']);
                    $this->niveauRepository->addOrUpdate($niveau);
                    $niveauxMap[$etd['Niveau']] = $niveau;
                }
                $newClasse->setNiveau($niveauxMap[$etd['Niveau']]);

                if (!isset($filieresMap[$etd['Filiere']])) {
                    $filiere = new Filiere();
                    $filiere->setArchived(false);
                    $filiere->setLibelle($etd['Filiere']);
                    $filiere->setEcole($ecole);
                    $this->filiereRepository->addOrUpdate($filiere);
                    $filieresMap[$etd['Filiere']] = $filiere;
                }
                $newClasse->setFiliere($filieresMap[$etd['Filiere']]);

                $newClasse->setEcole($ecole);
                $this->classeRepository->addOrUpdate($newClasse);

                $classesMap[$etd['Classe']] = $newClasse;
            }
        }
    }

    private function setListe(Ecole $ecole, $annee, $nom, $criteres): Liste
    {
        $liste = new Liste();
        $liste->setEcole($ecole)
            ->setAnnee($this->anneeRepository->find($annee))
            ->setDate(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')))
            ->setLibelle($nom)
            ->setCritere($criteres)
            ->setArchived(false);
        $this->listeRepository->addOrUpdate($liste);
        $newListe = $this->listeRepository->findByLibelle($nom);

        return $newListe;
    }
}
