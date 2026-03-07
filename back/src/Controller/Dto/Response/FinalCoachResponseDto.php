<?php

namespace App\Controller\Dto\Response;

use App\Entity\Coach;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

class FinalCoachResponseDto
{
    private int $id;
    private string $nom;
    private string $prenom;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

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


    public function toDto(User $coach, array $groupes): FinalCoachResponseDto
    {
        $dto = new FinalCoachResponseDto();

        $grps = [];
        foreach($groupes as $g){
            $grps[] = $g->getLibelle();
        }

        $dto->setId($coach->getId())
            ->setNom($coach->getNom())
            ->setPrenom($coach->getPrenom())
            ->setGroupes($grps);

        return $dto;
    }
}
