<?php

namespace App\Service;

use App\Entity\Etudiant;
use App\Entity\Liste;
use App\Entity\Jury;
use App\Repository\ListeRepository;
use App\Repository\JuryRepository;
use App\Repository\EtudiantRepository;
use App\Controller\Dto\Response\CoachResponseDto;
use App\Controller\Dto\Response\FinalJuryResponseDto;
use App\Controller\Dto\Response\GroupeResponseDto;
use App\Controller\Dto\Response\JuryResponseDto;

class JuryService{
    private $juryRepository;
    private $etudiantRepository;
    public function __construct(JuryRepository $juryRepository, EtudiantRepository $etudiantRepository)
    {
        $this->juryRepository= $juryRepository;
        $this->etudiantRepository= $etudiantRepository;
    }

    public function juryFinalCoachesDto($jurys):array
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

            $dtos[] = (new FinalJuryResponseDto())->toDto($jury, $coachs);
        }

        $results = [];
        foreach($dtos as $d){
            $results[] = [
                'id' => $d->getId(),
                'libelle' => $d->getLibelle(),
                'coachs' => $d->getCoachs()
            ];
        }

        return $results;
    }

    public function juryCoachesDto($jurys, ?array $groupesFinalistes = []):array
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
            if(count($groupesFinalistes)>0){foreach($groupesFinalistes as $group){ $grps[] = (new GroupeResponseDto())->toDto($group, []); }}
            else{foreach($jury->getGroupes() as $group){ $grps[] = (new GroupeResponseDto())->toDto($group, []); }}
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
