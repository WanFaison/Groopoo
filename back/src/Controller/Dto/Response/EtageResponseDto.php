<?php

namespace App\Controller\Dto\Response;

use App\Controller\Dto\RestResponse;
use App\Entity\Annee;
use App\Entity\Etage;
use App\Entity\Salle;
use App\Repository\AnneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EtageResponseDto
{
    private int $id;
    private string $libelle;
    private string $ecole;

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

    public function toDto(Etage $etage): EtageResponseDto
    {
        $dto = new EtageResponseDto;

        $dto->setId($etage->getId());
        $dto->setLibelle($etage->getLibelle());
        $dto->setEcole($etage->getEcole()->getLibelle());

        return $dto;
    }
}
