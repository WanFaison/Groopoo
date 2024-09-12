<?php

namespace App\Controller\Dto\Response;

use App\Controller\Dto\RestResponse;
use App\Entity\Annee;
use App\Repository\AnneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnneeResponseDto
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

    public function toDto(Annee $annee): AnneeResponseDto
    {
        $dto = new AnneeResponseDto;

        $dto->setId($annee->getId());
        $dto->setLibelle($annee->getLibelle());
        $dto->setArchived($annee->isArchived());

        return $dto;
    }
}
