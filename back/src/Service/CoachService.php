<?php

namespace App\Service;

use App\Entity\Coach;
use App\Entity\Liste;
use App\Entity\Groupe;
use App\Entity\Jury;
use App\Entity\User;
use App\Enums\Role;
use App\Repository\CoachRepository;
use App\Repository\GroupeRepository;
use App\Repository\ListeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class CoachService{
    private $entityManager;
    private $listeRepository;
    private $groupeRepository;
    private $userRepository;
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, GroupeRepository $groupeRepository, ListeRepository $listeRepository)
    {
        $this->entityManager = $entityManager;
        $this->listeRepository = $listeRepository;
        $this->groupeRepository = $groupeRepository;
        $this->userRepository = $userRepository;
    }

    public function checkCoachInListe(User $coach, Liste $liste)
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

    public function convertJsonDataToCoachArray(array $coachs):array {
        $coachArray = [];
        foreach($coachs as $c){
            $cc = $this->userRepository->find($c['id']);
            $cc ? $coachArray[]=$cc : null;
        }
        return $coachArray;
    }

    public function makeJury(Liste $liste, array $coaches)
    {
        foreach($liste->getJuries() as $jury){
            $this->entityManager->remove($jury);
        }
        $this->entityManager->flush();
        $this->listeRepository->addOrUpdate($liste);

        $cces = $this->userRepository->findAllByEcoleOrRole($liste->getEcole(), Role::COACH->value);
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

    public function getAllCoachInListe(Liste $liste): array
    {
        $coaches = [];
        $groupes = $this->groupeRepository->findAllByListe($liste);
        foreach($groupes as $grp){
            !in_array($grp->getCoach(), $coaches) ? array_push($coaches, $grp->getCoach()): null;
        }
        
        return $coaches;
    }
}
