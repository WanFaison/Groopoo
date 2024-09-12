<?php

namespace App\Controller\Dto\Response;

use App\Entity\Classe;

class ClasseResponseDto
{
    private int $id;
    private string $libelle;
    private string $filiere;
    private string $niveau;
    private int $effectif;

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

    public function getFiliere(): ?string
    {
        return $this->filiere;
    }

    public function setFiliere(string $filiere): static
    {
        $this->filiere = $filiere;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getEffectif(): ?int
    {
        return $this->effectif;
    }
    public function setEffectif(int $effectif): static
    {
        $this->effectif = $effectif;
        return $this;
    }

    public function toDto(Classe $classe): ClasseResponseDto
    {
        $dto = new ClasseResponseDto();

        $dto->setId($classe->getId());
        $dto->setLibelle($classe->getLibelle());
        $dto->setFiliere($classe->getFiliere()->getLibelle());
        $dto->setNiveau($classe->getNiveau()->getLibelle());
        $dto->setEffectif($classe->getEffectif());

        return $dto;
    }
}
