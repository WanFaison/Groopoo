<?php

namespace App\Controller\Dto\Response;

use App\Entity\Liste;
use App\Repository\AnneeRepository;
use App\Repository\EcoleRepository;
use Doctrine\ORM\Mapping as ORM;

class ListeResponseDto
{
    private int $id;
    private string $libelle;
    private bool $isArchived;
    private string $annee;
    private string $ecole;
    private string $date;

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

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): static
    {
        $this->annee = $annee;

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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function toDto(Liste $liste): ListeResponseDto
    {
        $dto = new ListeResponseDto();

        $dto->setId($liste->getId());
        $dto->setLibelle($liste->getLibelle());
        $dto->setArchived($liste->isArchived());
        $dto->setAnnee($liste->getAnnee()->getLibelle());
        $dto->setEcole($liste->getEcole()->getLibelle());
        $dto->setDate($liste->getDate()->format('d-m-Y'));

        return $dto;
    }
}
