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
    private float $note;
    private string $salle;
    private string $coach;

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

    public function getNote(): ?float
    {
        return $this->note;
    }
    public function setNote(float $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getSalle(): ?string
    {
        return $this->salle;
    }
    public function setSalle(string $salle): static
    {
        $this->salle = $salle;

        return $this;
    }

    public function getCoach(): ?string
    {
        return $this->coach;
    }
    public function setCoach(string $coach): static
    {
        $this->coach = $coach;

        return $this;
    }

    public function toDto(Groupe $groupe, array $etds): GroupeResponseDto
    {
        $dto = new GroupeResponseDto();

        $dto->setId($groupe->getId())
            ->setLibelle($groupe->getLibelle())
            ->setListe($groupe->getListe()->getId())
            ->setListeT($groupe->getListe()->getLibelle())
            ->setEtudiants($etds)
            ->setSalle($groupe->getSalle() ? $groupe->getSalle()->getLibelle() : '')
            ->setCoach($groupe->getCoach() ? $groupe->getCoach()->getNom(). ' ' .$groupe->getCoach()->getPrenom() : '');
        $n = $groupe->getNote();
        $dto->setNote($n !== null ? $n : 0);

        return $dto;
    }
}
