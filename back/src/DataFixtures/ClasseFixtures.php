<?php

namespace App\DataFixtures;

use App\Entity\Classe;
use App\Repository\FiliereRepository;
use App\Repository\NiveauRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ClasseFixtures extends Fixture implements DependentFixtureInterface
{
    private $niveauRepository;
    private $filiereRepository;
    public function  __construct(NiveauRepository $niveauRepository, FiliereRepository $filiereRepository){
          $this->niveauRepository=$niveauRepository;
          $this->filiereRepository=$filiereRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $filieres=$this->filiereRepository->findAll();
        $niveaux=$this->niveauRepository->findAll();
        $classeArray =[];
        for ($i = 0; $i < count($niveaux); $i++) {
            for ($j = 0; $j < count($filieres); $j++){
                $classe = new Classe();
                $classe->setNiveau($niveaux[$i]);
                $classe->setFiliere($filieres[$j]);
                $classe->setLibelle($classe->getNiveau()->getLibelle().''.$classe->getFiliere()->getLibelle());
                $classe->setArchived(false);
                $classe->setEffectif(0);
                $classe->setEcole($classe->getFiliere()->getEcole());
            
                $classeArray[] = $classe;
            }
            
        }
        foreach($classeArray as $classe){
            $manager->persist($classe); 
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [FiliereFixtures::class];
    }
}
