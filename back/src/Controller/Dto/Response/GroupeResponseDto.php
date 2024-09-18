<?php

namespace App\Controller\Dto\Response;

use App\Entity\Groupe;

class GroupeResponseDto
{
    private int $id;
    private string $libelle;
    private int $liste;
    private string $listeT;
    private array $etudiants;
    private string $note;

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

    public function getListe(): ?int
    {
        return $this->liste;
    }
    public function setListe(int $liste): static
    {
        $this->liste = $liste;
        return $this;
    }

    public function getListeT(): ?string
    {
        return $this->listeT;
    }
    public function setListeT(string $listeT): static
    {
        $this->listeT = $listeT;

        return $this;
    }

    public function getEtudiants(): ?array
    {
        return $this->etudiants;
    }

    public function setEtudiants(array $etudiants): static
    {
        $this->etudiants = $etudiants;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function toDto(Groupe $groupe, array $etds): GroupeResponseDto
    {
        $dto = new GroupeResponseDto();

        $dto->setId($groupe->getId());
        $dto->setLibelle($groupe->getLibelle());
        $dto->setListe($groupe->getListe()->getId());
        $dto->setListeT($groupe->getListe()->getLibelle());
        $dto->setEtudiants($etds);
        $dto->setNote(strval($groupe->getNote()));

        return $dto;
    }
}
