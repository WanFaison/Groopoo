<?php

namespace App\Service;

use App\Controller\Dto\Response\UserResponseDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService{
    private $entityManager;
    private $userRepository;
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function userListeDto($users):array
    {
        $dtos = [];
        foreach($users as $u){
            $roles = [];
            foreach($u->getRoles() as $r){
                $roles[] = $r;
            }

            $dtos[] = (new UserResponseDto())->toDto($u, $roles);
        }

        $results = [];
        foreach($dtos as $d){
            $results[] = [
                'id' => $d->getId(),
                'username' => $d->getUsername(),
                'noms' => $d->getNoms(),
                'email' => $d->getEmail(),
                'ecole' => $d->getEcole(),
                'ecoleT' => $d->getEcoleT(),
                'roles' => $d->getRoles(), 
            ];
        }

        return $results;
    }
}
