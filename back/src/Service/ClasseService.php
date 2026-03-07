<?php

namespace App\Service;

use App\Entity\Etudiant;
use App\Entity\Liste;
use App\Entity\Classe;
use App\Repository\ListeRepository;
use App\Repository\ClasseRepository;
use App\Repository\EtudiantRepository;

class ClasseService{
    private $classRepository;
    private $etudiantRepository;
    public function __construct(ClasseRepository $classRepository, EtudiantRepository $etudiantRepository)
    {
        $this->classRepository= $classRepository;
        $this->etudiantRepository= $etudiantRepository;
    }

    public function getAllClassesInListe(Liste $liste): array
    {
        $classes = [];
        $etudiants = $this->etudiantRepository->findAllByListe($liste);
        foreach($etudiants as $etd){
            in_array($etd->getClasse(), $classes, false) ? array_push($classes, $etd->getClasse()): null;
        }

        return $classes;
    }

}
