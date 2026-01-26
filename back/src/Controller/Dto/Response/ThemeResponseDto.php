<?php

namespace App\Controller\Dto\Response;

use App\Entity\Theme;
use Doctrine\ORM\Mapping as ORM;

class ThemeResponseDto
{
    private int $id;
    private string $libelle;
    private bool $isArchived;

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


    public function toDto(Theme $theme): ThemeResponseDto
    {
        $dto = new ThemeResponseDto();

        $dto->setId($theme->getId());
        $dto->setLibelle($theme->getLibelle());
        $dto->setArchived($theme->isArchived());

        return $dto;
    }
}
