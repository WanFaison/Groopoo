<?php

namespace App\DataFixtures;

use App\Entity\Annee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AnneeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $annee = new Annee();
            $annee->setLibelle((2021 + $i));
            $annee->setArchived(false);
            $manager->persist($annee);
        }

        $manager->flush();
    }
}
