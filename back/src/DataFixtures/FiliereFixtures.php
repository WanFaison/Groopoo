<?php

namespace App\DataFixtures;

use App\Entity\Filiere;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Repository\EcoleRepository;

class FiliereFixtures extends Fixture
{
    private $ecoleRepository;
    public function  __construct(EcoleRepository $ecoleRepository){
        $this->ecoleRepository=$ecoleRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $ecoles = $this->ecoleRepository->findAll();
        $filiereArray = []; 

        for ($i = 0; $i < count($ecoles); $i++) {
            for ($j = 0; $j < 7; $j++) {
                $filiere = new Filiere();
                $filiere->setLibelle('Filiere ' . $j);
                $filiere->setArchived(false);
                $filiere->setEcole($ecoles[$i]);
                
                $filiereArray[] = $filiere;
            }
        }
        foreach ($filiereArray as $filiere) {
            $manager->persist($filiere);
        }

        $manager->flush();
    }
}
