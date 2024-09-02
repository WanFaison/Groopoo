<?php

namespace App\DataFixtures;

use App\Entity\Liste;
use App\Repository\AnneeRepository;
use App\Repository\EcoleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ListeFixtures extends Fixture implements DependentFixtureInterface
{
    private $anneeRepository;
    private $ecoleRepository;
    public function  __construct(AnneeRepository $anneeRepository, EcoleRepository $ecoleRepository){
          $this->anneeRepository=$anneeRepository;
          $this->ecoleRepository=$ecoleRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $annees=$this->anneeRepository->findAll();
        $ecoles=$this->ecoleRepository->findAll();
        for ($i = 0; $i < 4; $i++) {
            $liste = new Liste();
            $liste->setLibelle('Liste ' . $i);
            $liste->setAnnee($annees[0]);
            $liste->setEcole($ecoles[$i]);
            $manager->persist($liste);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [AnneeFixtures::class, EcoleFixtures::class];
    }
}
