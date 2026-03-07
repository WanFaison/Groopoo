<?php

namespace App\Service;

use App\Entity\Groupe;
use App\Entity\Liste;
use App\Entity\Etudiant;
use App\Entity\Filiere;
use App\Entity\Classe;
use App\Entity\Niveau;
use App\Entity\Ecole;
use App\Repository\GroupeRepository;
use App\Repository\FiliereRepository;
use App\Repository\NiveauRepository;
use App\Repository\ClasseRepository;
use App\Repository\EcoleRepository;
use App\Repository\AnneeRepository;
use App\Repository\ListeRepository;
use App\Repository\EtudiantRepository;
use App\Repository\AbsenceRepository;
use App\Controller\Dto\Response\EtudiantResponseDto;
use App\Controller\Dto\Request\EtudiantRequestDto;
use App\Controller\Dto\Response\GroupeResponseDto;
use Doctrine\ORM\EntityManagerInterface;

class GroupeService{
    private $entityManager;
    private $groupeRepository;
    private $filiereRepository;
    private $niveauRepository;
    private $classeRepository;
    private $ecoleRepository;
    private $anneeRepository;
    private $listeRepository;
    private $etudiantRepository;
    private $absenceRepository;
    private $coachService;
    public function __construct(EntityManagerInterface $entityManager, CoachService $coachService, AbsenceRepository $absenceRepository, EtudiantRepository $etudiantRepository, ListeRepository $listeRepository, AnneeRepository $anneeRepository, EcoleRepository $ecoleRepository, ClasseRepository $classeRepository, NiveauRepository $niveauRepository, FiliereRepository $filiereRepository, GroupeRepository $groupeRepository)
    {
        $this->entityManager = $entityManager;
        $this->groupeRepository = $groupeRepository;
        $this->filiereRepository = $filiereRepository;
        $this->niveauRepository = $niveauRepository;
        $this->classeRepository = $classeRepository;
        $this->ecoleRepository = $ecoleRepository;
        $this->listeRepository = $listeRepository;
        $this->etudiantRepository = $etudiantRepository;
        $this->absenceRepository = $absenceRepository;
        $this->anneeRepository = $anneeRepository;
        $this->coachService = $coachService;
    }

    public function reDoGrps($criteres, $etudiants, $taille, Liste $liste)
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
                                        $g->addEtudiant($group[count($group) - 1]);
                                        unset($etds[array_search($group[count($group) - 1], $etds)]);
                                        $etds = array_values($etds);
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
                $this->entityManager->persist($g);
                $tlaEtds -= count($g->getEtudiant());

                if($num % 10 === 0){
                    $this->entityManager->flush();
                }
                $num++; 
            }
            $this->entityManager->flush();
        }
    }

    private function clearEtds(Groupe $groupe): void
    {
        $etudiants = $groupe->getEtudiant();

        foreach ($etudiants as $etudiant) {
            $groupe->removeEtudiant($etudiant); 
        }
        $this->entityManager->flush();
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
        }   

        return false;
    }

    public function grpJourDto($groups, $jour):array
    {
        $results = [];
        foreach($groups as $g){
            $etds = [];
            foreach($g->getEtudiant() as $e){
                $eds = (new EtudiantRequestDto())->toDto($e, $this->absenceRepository->findAllByJourAndEtudiant($jour, $e));
                $etds[] = [
                    'id' => $eds->getId(),
                    'matricule' => $eds->getMatricule(),
                    'nom' => $eds->getNom(),
                    'prenom' => $eds->getPrenom(),
                    'classe' => $eds->getClasse(),
                    'groupe' => $eds->getGroupe(),
                    'emargement1' => $eds->getEmargement1(),
                    'emargement2' => $eds->getEmargement2()
                ];
            }

            $dto = (new GroupeResponseDto())->toDto($g, $etds);
            $results[] = [
                'id' => $dto->getId(),
                'libelle' => $dto->getLibelle(),
                'liste' => $dto->getListe(),
                'listeT' => $dto->getListeT(),
                'etudiants' => $dto->getEtudiants(),
                'note' => $dto->getNote()
            ];
        }

        return $results;
    }

    public function grpListeDto($groups):array
    {
        $results = [];
        foreach($groups as $g){
            $etds = [];
            foreach($g->getEtudiant() as $e){
                $eds = (new EtudiantResponseDto())->toDto($e);
                $etds[] = [
                    'id' => $eds->getId(),
                    'matricule' => $eds->getMatricule(),
                    'nom' => $eds->getNom(),
                    'prenom' => $eds->getPrenom(),
                    'sexe' => $eds->getSexe(),
                    'classe' => $eds->getClasse(),
                    'niveau' => $eds->getNiveau(),
                    'filiere' => $eds->getFiliere(),
                    'groupe' => $eds->getGroupe(),
                    'noteEtd' => $eds->getNoteEtd(),
                    'noteFinal' => $eds->getNoteFinal()
                ];
            }

            $dto = (new GroupeResponseDto())->toDto($g, $etds);
            $results[] = [
                'id' => $dto->getId(),
                'libelle' => $dto->getLibelle(),
                'liste' => $dto->getListe(),
                'listeT' => $dto->getListeT(),
                'etudiants' => $dto->getEtudiants(),
                'note' => $dto->getNote(),
                'coach' => $dto->getCoach(),
                'salle' => $dto->getSalle(),
                'theme' => $dto->getTheme()
            ];
        }

        return $results;
    }

    public function manageData($ecoleId, $annee, $taille, $nom, $etudiants, $criteres, $state): ?Liste
    {
        $ecole = $this->ecoleRepository->find($ecoleId);
        $this->setClasses($etudiants, $ecole); //fonction qui vérifie si toutes les classes de la liste existent dans la base de données (si ce n'est pas le cas, elle les crée)
        $newListe = $this->setListe($ecole, $annee, $nom, $criteres);
        $newEtds = $this->loadEtudiants($etudiants, $ecole);

        if($state == 0){$this->loadGroups($criteres, $newEtds, $taille, $newListe);}
        else{$this->loadGroupsPerClasse($newEtds, $taille, $newListe);}
        
        return $newListe;
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
            if (!isset($classesMap[trim($etd['Classe'])])) {
                $newClasse = new Classe();
                $newClasse->setArchived(false);
                $newClasse->setLibelle(trim($etd['Classe']));

                if (!isset($niveauxMap[trim($etd['Niveau'])])) {
                    $niveau = new Niveau();
                    $niveau->setArchived(false);
                    $niveau->setLibelle(trim($etd['Niveau']));
                    $this->niveauRepository->addOrUpdate($niveau);
                    $niveauxMap[trim($etd['Niveau'])] = $niveau;
                }
                $newClasse->setNiveau($niveauxMap[trim($etd['Niveau'])]);

                if (!isset($filieresMap[trim($etd['Filiere'])])) {
                    $filiere = new Filiere();
                    $filiere->setArchived(false);
                    $filiere->setLibelle(trim($etd['Filiere']));
                    $filiere->setEcole($ecole);
                    $this->filiereRepository->addOrUpdate($filiere);
                    $filieresMap[trim($etd['Filiere'])] = $filiere;
                }
                $newClasse->setFiliere($filieresMap[trim($etd['Filiere'])]);

                $newClasse->setEcole($ecole);
                $this->classeRepository->addOrUpdate($newClasse);

                $classesMap[trim($etd['Classe'])] = $newClasse;
            }
        }
    }

    public function setListe(Ecole $ecole, $annee, $nom, $criteres): Liste
    {
        $liste = new Liste();
        $liste->setEcole($ecole)
            ->setAnnee($this->anneeRepository->find($annee))
            ->setDate(\DateTime::createFromFormat('Y-m-d', date('Y-m-d')))
            ->setLibelle(trim($nom))
            ->setCritere($criteres)
            ->setArchived(false)
            ->setComplete(false)
            ->setImported(false);
        $this->listeRepository->addOrUpdate($liste);
        $newListe = $this->listeRepository->findByLibelle($nom);

        return $newListe;
    }

    private function loadEtudiants($etudiants, Ecole $ecole): array
    {
        $existingClasses = $this->classeRepository->findAllByEcole($ecole);
        $classesMap = [];
        foreach ($existingClasses as $classe) {
            $classesMap[$classe->getLibelle()] = $classe;
        }

        $newEtudiants = [];
        $batchSize = 250;
        $i = 1;
        foreach ($etudiants as $key => $etudiant) {
            $newEtd = new Etudiant();
            $newEtd->setArchived(false)
                    ->setClasse($classesMap[trim($etudiant['Classe'])]);

            $theEtd = $this->etudiantRepository->findByMatricule(trim($etudiant['Matricule']));
            if($theEtd){                
                $newEtd->setMatricule($theEtd->getMatricule())
                    ->setNom($theEtd->getNom())
                    ->setPrenom($theEtd->getPrenom())
                    ->setSexe($theEtd->getSexe());
            }else{
                $newEtd->setMatricule(trim($etudiant['Matricule']))
                    ->setNom($etudiant['Nom'])
                    ->setPrenom($etudiant['Prenom'])
                    ->setSexe(trim($etudiant['Sexe']));
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

            if(!empty($criteres)){
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

    private function loadGroupsPerClasse($etudiants, $taille, Liste $liste): void
    {
        $existingClasses = [];
        foreach($etudiants as $e){
            in_array($e->getClasse(), $existingClasses, false) ? null : $existingClasses[] = $e->getClasse();
        }

        $etds = $etudiants;
        shuffle($etds);
        $num = 1;
        foreach($existingClasses as $classe){
            $filteredEtds = array_filter($etds, function (Etudiant $etudiant) use ($classe) {
                return $etudiant->getClasse() === $classe;
                });
            
            if(count($filteredEtds)>0){
                $group = [];
                $newGrp = new Groupe();
                $newGrp->setArchived(false)
                        ->setListe($liste)
                        ->setLibelle('Groupe '.$num);

                while((count($group) < $taille) && (count($filteredEtds) > 0)){
                    $etd = array_shift($filteredEtds);
                    $group[] = $etd;
                    $newGrp->addEtudiant($etd);
                }
                $newGrp->setTaille(count($newGrp->getEtudiant()));
                $this->entityManager->persist($newGrp);

                if($num % 10 === 0){
                    $this->entityManager->flush();
                }
                $num++; 
            }
            $this->entityManager->flush();
        }
    }

    public function manageImports(array $etudiantGroups, Liste $newListe, Ecole $ecole)
    {
        $existingClasses = $this->classeRepository->findAllByEcole($ecole);
        $classesMap = [];
        foreach ($existingClasses as $classe) {
            $classesMap[$classe->getLibelle()] = $classe;
        }

        foreach($etudiantGroups as $key => $grp){
            $cnt = 0;
            $newGrp = new Groupe();
            $newGrp->setArchived(false)
                    ->setListe($newListe)
                    ->setNote((float) $grp['groupNote'])
                    ->setLibelle($grp['groupName']);
            $this->entityManager->persist($newGrp);

            foreach($grp['etudiants'] as $etd){
                $cnt++;
                $newEtd = new Etudiant();
                $newEtd->setArchived(false)
                        ->setMatricule($etd['matricule'])
                        ->setNom($etd['nom'])
                        ->setPrenom($etd['prenom'])
                        ->setSexe($etd['sexe'])
                        ->setClasse($classesMap[$etd['classe']]);
                $newGrp->addEtudiant($newEtd);
                $this->entityManager->persist($newEtd);
            }
            $newGrp->setTaille($cnt);
            $this->entityManager->persist($newGrp);
            $this->entityManager->flush();
        }
    }

    private function cleanWhenError(Liste $liste)
    {
        foreach($liste->getGroupes() as $grp){
            foreach($grp->getEtudiant() as $etd){
                $this->entityManager->remove($etd);
            }
            $this->entityManager->remove($grp);
            $this->entityManager->flush();
        }
        $this->entityManager->remove($liste);
        $this->entityManager->flush();
    }

    public function getTop100GroupesByNote(array $groupes): array
    {
        usort($groupes, function (Groupe $a, Groupe $b) {
            $noteA = $a->getNote() ?? 0.0;
            $noteB = $b->getNote() ?? 0.0;

            return $noteB <=> $noteA; // Descending order
        });

        return array_slice($groupes, 0, 100);
    }

    public function getTopNGroupesInArray(array $array, int $n): array
    {
        usort($array, function ($a, $b) {
            return $b->getNote() <=> $a->getNote();
        });

        return array_slice($array, 0, $n);
    }

    public function getArrayTopNPerCoach(Liste $liste, int $n): array
    {
        $groupes = [];
        foreach($this->coachService->getAllCoachInListe($liste) as $coach){
            $grps = $this->groupeRepository->findAllByCoachListe($coach, $liste);
            foreach($this->getTopNGroupesInArray($grps, $n) as $grp){
                array_push($groupes, $grp);
            }
        }

        return $groupes;
    }
}
