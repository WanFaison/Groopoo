<?php

namespace App\Controller\Dto\Response;

use App\Entity\Groupe;

class FinalGroupeResponseDto
{
    private int $id;
    private string $libelle;
    private float $note;
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

    public function getNote(): ?float
    {
        return $this->note;
    }
    public function setNote(float $note): static
    {
        $this->note = $note;

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

    public function toDto(Groupe $groupe): FinalGroupeResponseDto
    {
        $dto = new FinalGroupeResponseDto();

        $dto->setId($groupe->getId())
            ->setLibelle($groupe->getLibelle())
            ->setCoach($groupe->getCoach()->getNom().' '.$groupe->getCoach()->getPrenom());
        $n = $groupe->getNote();
        $dto->setNote($n !== null ? $n : 0);

        return $dto;
    }
}
