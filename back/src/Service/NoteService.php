<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class NoteService{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getTop10(array $groupes): array
    {
        usort($groupes, function ($a, $b) {
            return $b->getNote() <=> $a->getNote(); 
        });

        return array_slice($groupes, 0, 10);
    }

    public function setToFinal(array $groupes, bool $final): array
    {
        foreach($groupes as $grp){
            $grp->setFinal($final);
            $this->entityManager->persist($grp);
        }
        $this->entityManager->flush();
        return $groupes;
    }
}
