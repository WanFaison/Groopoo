<?php

namespace App\Controller\Dto\Response;

use App\Entity\User;

class UserResponseDto{
    private int $id;
    private string $email;
    private ?int $ecole = null;
    private ?string $ecoleT = null;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEcole(): ?int
    {
        return $this->ecole;
    }
    public function setEcole(int $ecole): static
    {
        $this->ecole = $ecole;

        return $this;
    }
    
    public function getEcoleT(): ?string
    {
        return $this->ecoleT;
    }
    public function setEcoleT(string $ecoleT): static
    {
        $this->ecoleT = $ecoleT;

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
        $dto->setEmail($user->getEmail());
        $dto->setRoles($roles);
        if($user->getEcole()){
            $dto->setEcole($user->getEcole()->getId());
            $dto->setEcoleT($user->getEcole()->getLibelle());
        }

        return $dto;
    }
}
