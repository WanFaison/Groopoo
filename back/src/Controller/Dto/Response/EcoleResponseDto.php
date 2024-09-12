<?php

namespace App\Controller\Dto\Response;

use App\Controller\Dto\RestResponse;
use App\Entity\Ecole;
use App\Repository\EcoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EcoleResponseDto
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

    public function toDto(Ecole $ecole): EcoleResponseDto
    {
        $dto = new EcoleResponseDto;

        $dto->setId($ecole->getId());
        $dto->setLibelle($ecole->getLibelle());
        $dto->setArchived($ecole->isArchived());

        return $dto;
    }
}
