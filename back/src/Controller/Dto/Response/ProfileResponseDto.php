<?php

namespace App\Controller\Dto\Response;

use App\Entity\Profile;

class ProfileResponseDto
{
    private int $id;
    private string $libelle;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }
    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function toDto(Profile $profile): ProfileResponseDto
    {
        $dto = new ProfileResponseDto();

        $dto->setId($profile->getId());
        $dto->setLibelle($profile->getLibelle());

        return $dto;
    }
}
