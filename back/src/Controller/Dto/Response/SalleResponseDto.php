<?php

namespace App\Controller\Dto\Response;

use App\Controller\Dto\RestResponse;
use App\Entity\Annee;
use App\Entity\Salle;
use App\Repository\AnneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SalleResponseDto
{
    private int $id;
    private string $libelle;
    private string $ecole;
    private string $etage;

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

    public function getEcole(): ?string
    {
        return $this->ecole;
    }

    public function setEcole(string $ecole): static
    {
        $this->ecole = $ecole;

        return $this;
    }

    public function getEtage(): ?string
    {
        return $this->etage;
    }

    public function setEtage(string $etage): static
    {
        $this->etage = $etage;

        return $this;
    }

    public function toDto(Salle $salle): SalleResponseDto
    {
        $dto = new SalleResponseDto;

        $dto->setId($salle->getId());
        $dto->setLibelle($salle->getLibelle());
        $dto->setEcole($salle->getEtage()->getEcole()->getLibelle());
        $dto->setEtage($salle->getEtage()->getLibelle());

        return $dto;
    }
}
