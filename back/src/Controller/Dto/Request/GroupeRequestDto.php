<?php

namespace App\Controller\Dto\Request;

use App\Entity\Groupe;

class GroupeRequestDto
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

    public function toDto(Groupe $groupe): GroupeRequestDto
    {
        $dto = new GroupeRequestDto();

        $dto->setId($groupe->getId());
        $dto->setLibelle($groupe->getLibelle());

        return $dto;
    }
}
