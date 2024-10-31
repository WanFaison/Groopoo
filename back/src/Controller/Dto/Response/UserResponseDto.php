<?php

namespace App\Controller\Dto\Response;

use App\Entity\User;

class UserResponseDto{
    private int $id;
    private string $username;
    private string $email;
    private ?array $ecole = null;
    private ?array $ecoleT = null;
    private array $roles;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEcole(): ?array
    {
        return $this->ecole;
    }
    public function addEcole(int $ecole): static
    {
        if ($this->ecole === null) {
            $this->ecole = [];
        }
    
        if (!in_array($ecole, $this->ecole, true)) { 
            $this->ecole[] = $ecole;
        }

        return $this;
    }
    
    public function getEcoleT(): ?array
    {
        return $this->ecoleT;
    }
    public function addEcoleT(string $ecoleT): static
    {
        if ($this->ecoleT === null) {
            $this->ecoleT = [];
        }
    
        if (!in_array($ecoleT, $this->ecoleT, true)) { 
            $this->ecoleT[] = $ecoleT;
        }

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function toDto(User $user, array $roles): UserResponseDto
    {
        $dto = new UserResponseDto();

        $dto->setId($user->getId());
        $dto->setUsername($user->getUsername());
        $dto->setEmail($user->getEmail());
        $dto->setRoles($roles);
        if($user->getEcoles()){
            foreach($user->getEcoles() as $e){
                $dto->addEcole($e->getId());
                $dto->addEcoleT($e->getLibelle());
            }
        }

        return $dto;
    }
}
