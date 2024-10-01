<?php

namespace App\Controller;

use App\Controller\Dto\Response\EtudiantResponseDto;
use App\Controller\Dto\Response\GroupeResponseDto;
use App\Controller\Dto\RestResponse;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Filiere;
use App\Entity\Groupe;
use App\Entity\Liste;
use App\Entity\Niveau;
use App\Repository\AnneeRepository;
use App\Repository\ClasseRepository;
use App\Repository\EcoleRepository;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use App\Repository\GroupeRepository;
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

    public function __construct(EntityManagerInterface $entityManager, EcoleRepository $ecoleRepository, AnneeRepository $anneeRepository, EtudiantRepository $etudiantRepository, NiveauRepository $niveauRepository, FiliereRepository $filiereRepository, ClasseRepository $classeRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
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
                    'groupe' => $e->getGroupe()
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
                if(!empty($criteres)){
                    if(count($criteres['filiere'])>0){
                        foreach ($criteres['filiere'] as $crit) {
                            if(isset($crit['choix']) || trim($crit['choix']) != ''){
                                if($crit['choix'] == -1){
                                    $fnum = 0;
                                    while((count($group) < $taille) && (count($etds) > 0)){
                                        if(!$this->checkLoad($group, 'filiere', $etds[$fnum]->getClasse()->getFiliere(), 1)){
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
                    }
        
                    if(count($criteres['classe'])>0){
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
                    }
        
                    if(count($criteres['niveau'])>0){
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
            $newListe = $this->manageData($ecole, $annee, $taille, $nom, $etudiants, $criteres, 'new');
        }

        try {
            return RestResponse::requestResponse('Data received and list created', $newListe->getId(), JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
        
    }

    private function manageData($ecole, $annee, $taille, $nom, $etudiants, $criteres, $state = 'redo'): ?Liste
    {
        $newListe = $this->setListe($ecole, $annee, $nom, $criteres);
        if($state == 'new'){
            $this->setClasses($etudiants, $ecole);
            $newEtds = $this->loadEtudiants($etudiants);
            $this->loadGroups($criteres, $newEtds, $taille, $newListe);
        }else{
            $newEtds = $this->loadEtudiants($etudiants, 'redo');
            $this->loadGroups($criteres, $newEtds, $taille, $newListe);
        }
        
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
            $newGrp->setArchived(false);
            $newGrp->setListe($liste);
            $newGrp->setLibelle('Groupe '.$num);

            if(!empty($criteres)){
                if(count($criteres['filiere'])>0){
                    foreach ($criteres['filiere'] as $crit) {
                        if(isset($crit['choix']) || trim($crit['choix']) != ''){
                            if($crit['choix'] == -1){
                                $fnum = 0;
                                while((count($group) < $taille) && (count($etds) > 0)){
                                    if(!$this->checkLoad($group, 'filiere', $etds[$fnum]->getClasse()->getFiliere(), 1)){
                                        $group[] = $etds[$fnum];
                                        $newGrp->addEtudiant($group[count($group) - 1]);
                                        unset($etds[array_search($group[count($group) - 1], $etds)]);
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
                }
    
                if(count($criteres['classe'])>0){
                    foreach ($criteres['classe'] as $crit) {
                        if(isset($crit['choix']) || trim($crit['choix']) != ''){
                            if($crit['choix'] == -1){
                                $fnum = 0;
                                while((count($group) < $taille) && (count($etds) > 0)){
                                    if(!$this->checkLoad($group, 'classe', $etds[$fnum]->getClasse(), 1)){
                                        $group[] = $etds[$fnum];
                                        $newGrp->addEtudiant($group[count($group) - 1]);
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
                                        $newGrp->addEtudiant($group[count($group) - 1]);
                                        unset($etds[array_search($group[count($group) - 1], $etds)]);
                                        $etds = array_values($etds);
                                    }
                                    
                                }
                            }
                        
                        }
                    }
                }
    
                if(count($criteres['niveau'])>0){
                    foreach ($criteres['niveau'] as $crit) {
                        if(isset($crit['choix']) || trim($crit['choix']) != ''){
                            if($crit['choix'] == -1){
                                $fnum = 0;
                                while((count($group) < $taille) && (count($etds) > 0)){
                                    if(!$this->checkLoad($group, 'niveau', $etds[$fnum]->getClasse()->getNiveau(), 1)){
                                        $group[] = $etds[$fnum];
                                        $newGrp->addEtudiant($group[count($group) - 1]);
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
                                        $newGrp->addEtudiant($group[count($group) - 1]);
                                        unset($etds[array_search($group[count($group) - 1], $etds)]);
                                        $etds = array_values($etds);
                                    }
                                    
                                }
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
            $this->groupeRepository->addOrUpdate($newGrp);
            $tlaEtds -= count($newGrp->getEtudiant());
            $num++; 
        }
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
        if(!empty($criteres)){
            if(count($criteres['niveau'])>0){
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
            }
            if(count($criteres['filiere'])>0){
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
            }
            if(count($criteres['classe'])>0){
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

    private function loadEtudiants($etudiants, $state = 'new'): array
    {
        $newEtudiants = [];
        foreach ($etudiants as $etudiant) {
            $newEtd = new Etudiant();
            $newEtd->setArchived(false);
            if($state == 'redo'){
                $newEtd->setMatricule($etudiant->getMatricule());
                $newEtd->setNom($etudiant->getNom());
                $newEtd->setPrenom($etudiant->getPrenom());
                $newEtd->setSexe($etudiant->getSexe());
                $newEtd->setNationalite($etudiant->getNationalite());
                $newEtd->setClasse($etudiant->getClasse());
            }else{
                if($this->etudiantRepository->checkExist($etudiant['Matricule'])){
                    $theEtd = $this->etudiantRepository->findByMatricule($etudiant['Matricule']);
                    
                    $newEtd->setMatricule($theEtd->getMatricule());
                    $newEtd->setNom($theEtd->getNom());
                    $newEtd->setPrenom($theEtd->getPrenom());
                    $newEtd->setSexe($theEtd->getSexe());
                    $newEtd->setNationalite($theEtd->getNationalite());
                    $newEtd->setClasse($theEtd->getClasse());
                }else{
                    $newEtd->setMatricule($etudiant['Matricule']);
                    $newEtd->setNom($etudiant['Nom']);
                    $newEtd->setPrenom($etudiant['Prenom']);
                    $newEtd->setSexe($etudiant['Sexe']);
                    $newEtd->setNationalite($etudiant['Nationalite']);
                    $newEtd->setClasse($this->classeRepository->findByLibelle($etudiant['Classe']));
                }
            }
            
            $this->etudiantRepository->addOrUpdate($newEtd);
            $newEtudiants[] = $this->etudiantRepository->findByMatricule($newEtd->getMatricule());
        }

        return $newEtudiants;
    }

    private function setClasses($etudiants, $ecole): void
    {
        foreach ($etudiants as $etd){
            if(!$this->classeRepository->checkExistByLibelle($etd['Classe'])){
                $newClasse = new Classe();
                $newClasse->setArchived(false);
                $newClasse->setLibelle($etd['Classe']);

                if(!$this->niveauRepository->checkExistByLibelle($etd['Niveau'])){
                    $niveau = new Niveau();
                    $niveau->setArchived(false);
                    $niveau->setLibelle($etd['Niveau']);
                    $this->niveauRepository->addOrUpdate($niveau);
                }
                if(!$this->filiereRepository->checkExistByLibelle($etd['Filiere'])){
                    $filiere = new Filiere();
                    $filiere->setArchived(false);
                    $filiere->setLibelle($etd['Filiere']);
                    $filiere->setEcole($this->ecoleRepository->find($ecole));
                    $this->filiereRepository->addOrUpdate($filiere);
                }
                
                $newClasse->setNiveau($this->niveauRepository->findByLibelle($etd['Niveau']));
                $newClasse->setFiliere($this->filiereRepository->findByLibelle($etd['Filiere']));
                $newClasse->setEcole($newClasse->getFiliere()->getEcole());
                $this->classeRepository->addOrUpdate($newClasse);
            }
        }
    }

    private function setListe($ecole, $annee, $nom, $criteres): Liste
    {
        $liste = new Liste();
        $liste->setEcole($this->ecoleRepository->find($ecole));
        $liste->setAnnee($this->anneeRepository->find($annee));
        $liste->setDate(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')));
        $liste->setLibelle($nom);
        $liste->setCritere($criteres);
        $liste->setArchived(false);
        $this->listeRepository->addOrUpdate($liste);
        $newListe = $this->listeRepository->findByLibelle($nom);

        return $newListe;
    }
}
