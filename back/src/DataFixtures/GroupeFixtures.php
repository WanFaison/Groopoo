<?php

namespace App\DataFixtures;

use App\Entity\Groupe;
use App\Repository\ListeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupeFixtures extends Fixture
{
    private $listeRespository;
    public function  __construct(ListeRepository $listeRespository){
          $this->listeRespository=$listeRespository;
    }

    public function load(ObjectManager $manager): void
    {
        // for ($i = 1; $i <= 4; $i++) {
        //     $groupe = new Groupe();
        //     $groupe->setLibelle('Groupe ' . $i);
        //     $groupe->setTaille(5);
        //     $groupe->setListe($liste);
        //     $manager->persist($groupe);
        // }
        $manager->flush();
    }
}
