<?php

namespace App\Controller\Dto\Response;

use App\Entity\Liste;
use App\Entity\Niveau;
use App\Repository\AnneeRepository;
use App\Repository\EcoleRepository;
use Doctrine\ORM\Mapping as ORM;

class NiveauResponseDto
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


    public function toDto(Niveau $niveau): NiveauResponseDto
    {
        $dto = new NiveauResponseDto();

        $dto->setId($niveau->getId());
        $dto->setLibelle($niveau->getLibelle());
        $dto->setArchived($niveau->isArchived());

        return $dto;
    }
}
