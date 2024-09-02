<?php

namespace App\DataFixtures;

use App\Entity\Ecole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EcoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 4; $i++) {
            $ecole = new Ecole();
            $ecole->setLibelle('Ecole ' . $i);
            $ecole->setArchived(false);
            $manager->persist($ecole);
        }
        $manager->flush();
    }
}
