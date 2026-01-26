<?php

namespace App\Controller\Dto\Response;

use App\Entity\Theme;
use Doctrine\ORM\Mapping as ORM;

class FinalThemeResponseDto
{
    private int $id;
    private string $libelle;
    private array $groupes;

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

    public function getGroupes(): ?array
    {
        return $this->groupes;
    }

    public function setGroupes(array $groupes): static
    {
        $this->groupes = $groupes;

        return $this;
    }


    public function toDto(Theme $theme, array $groupes): FinalThemeResponseDto
    {
        $dto = new FinalThemeResponseDto();

        $grps = [];
        foreach($groupes as $g){
            $grps[] = $g->getLibelle();
        }

        $dto->setId($theme->getId())
            ->setLibelle($theme->getLibelle())
            ->setGroupes($grps);

        return $dto;
    }
}
