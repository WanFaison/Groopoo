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
    private array $critere;
    private int $annee;
    private string $ecole;
    private string $date;
    private int $count;
    private bool $isComplete;
    private bool $isArchived;
    private bool $isImported;

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

    public function getCritere(): ?array
    {
        return $this->critere;
    }

    public function setCritere(array $critere): static
    {
        $this->critere = $critere;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
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

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function isComplete(): ?bool
    {
        return $this->isComplete;
    }

    public function setComplete(bool $isComplete): static
    {
        $this->isComplete = $isComplete;

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

    public function isImported(): ?bool
    {
        return $this->isImported;
    }

    public function setImported(bool $isImported): static
    {
        $this->isImported = $isImported;

        return $this;
    }

    public function toDto(Liste $liste): ListeResponseDto
    {
        $dto = new ListeResponseDto();

        $dto->setId($liste->getId());
        $dto->setLibelle($liste->getLibelle());
        $dto->setCritere($liste->getCritere());
        $dto->setAnnee($liste->getAnnee()->getId());
        $dto->setEcole($liste->getEcole()->getLibelle());
        $dto->setDate($liste->getDate()->format('d-m-Y'));
        $dto->setCount($liste->getGroupes()->count());
        $dto->setComplete($liste->isComplete());
        $dto->setArchived($liste->isArchived());
        $dto->setImported($liste->isImported());

        return $dto;
    }
}
