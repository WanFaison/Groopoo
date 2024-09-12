<?php

namespace App\DataFixtures;

use App\Entity\Niveau;
use App\Repository\NiveauRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NiveauFixtures extends Fixture
{
    private $niveauRepository;

    public function  __construct(NiveauRepository $niveauRepository){
        $this->niveauRepository=$niveauRepository;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 4; $i++) {
            $niv = new Niveau();
            $niv->setLibelle('Niveau ' . $i);
            $niv->setArchived(false);
            $manager->persist($niv);
        }

        $manager->flush();
    }
}
