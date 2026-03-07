<?php

namespace App\Controller\Dto\Response;

use App\Entity\Groupe;
use App\Entity\Jury;

class FinalJuryResponseDto
{
    private int $id;
    private string $libelle;
    private array $coachs;

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

    public function getCoachs(): ?array
    {
        return $this->coachs;
    }

    public function setCoachs(array $coachs): static
    {
        $this->coachs = $coachs;

        return $this;
    }

    public function toDto(Jury $jury, array $coachs): FinalJuryResponseDto
    {
        $dto = new FinalJuryResponseDto();

        $dto->setId($jury->getId())
            ->setLibelle($jury->getLibelle())
            ->setCoachs($coachs);

        return $dto;
    }
}
