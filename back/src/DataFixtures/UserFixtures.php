<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    public function  __construct(UserPasswordHasherInterface $encoder){
          $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $names=["Wan","Olympio"];
        $emails=["wan@band-it.sn","olympio@band-it.sn"];
        $role= "ROLE_ADMIN";
        $plainPassword = 'passer';
        for ($i = 0; $i < count($names); $i++) {
            $user = new User();
            $user->setUsername($names[$i]);
            $user->setEmail($emails[$i]);
            $encoded = $this->encoder->hashPassword($user,$plainPassword);
            $user->setPassword($encoded);
            $user->setRoles([$role]);
            $user->setArchived(false);
            $manager->persist($user);
            $this->addReference("User".$i, $user);
        }
         $manager->flush();
    }
}
