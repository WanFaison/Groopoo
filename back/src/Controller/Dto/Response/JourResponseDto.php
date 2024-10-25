<?php

namespace App\Controller\Dto\Response;

use App\Entity\Jour;
use DateTimeInterface;

class JourResponseDto
{
    private int $id;
    private string $libelle;
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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): static
    {
        $this->date = $date->format('Y-m-d');

        return $this;
    }

    public function toDto(Jour $jour): JourResponseDto
    {
        $dto = new JourResponseDto;
        $dto->setId($jour->getId());
        $dto->setLibelle($jour->getLibelle());
        $dto->setDate($jour->getDate());

        return $dto;
    }
}
