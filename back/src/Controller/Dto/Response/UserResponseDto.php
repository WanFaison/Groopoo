<?php

namespace App\Controller\Dto\Response;

use App\Entity\User;

class UserResponseDto{
    private int $id;
    private string $email;
    private ?int $ecole = null;
    private ?string $ecoleT = null;
    private array $profiles;

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

    public function getProfiles(): ?array
    {
        return $this->profiles;
    }
    public function setProfiles(array $profiles): static
    {
        $this->profiles = $profiles;

        return $this;
    }

    public function toDto(User $user, array $profiles): UserResponseDto
    {
        $dto = new UserResponseDto();

        $dto->setId($user->getId());
        $dto->setEmail($user->getEmail());
        $dto->setProfiles($profiles);
        if($user->getEcole()){
            $dto->setEcole($user->getEcole()->getId());
            $dto->setEcoleT($user->getEcole()->getLibelle());
        }

        return $dto;
    }
}
