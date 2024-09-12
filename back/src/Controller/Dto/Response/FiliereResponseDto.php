<?php

namespace App\Controller\Dto\Response;

use App\Entity\Filiere;

class FiliereResponseDto
{
    private int $id;
    private string $libelle;
    private bool $isArchived;

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

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function toDto(Filiere $filiere): FiliereResponseDto
    {
        $dto = new FiliereResponseDto();

        $dto->setId($filiere->getId());
        $dto->setLibelle($filiere->getLibelle());
        $dto->setArchived($filiere->isArchived());

        return $dto;
    }
}
