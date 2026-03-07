<?php

namespace App\Controller\Dto\Request;

use App\Controller\Dto\RestResponse;
use App\Entity\Annee;
use App\Entity\Coach;
use App\Entity\Etage;
use App\Entity\Salle;
use App\Entity\User;
use App\Repository\AnneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoachRequestDto
{
    private int $id;
    private string $nom;
    private string $prenom;

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


    public function toDto(User $coach): CoachRequestDto
    {
        $dto = new CoachRequestDto;

        $dto->setId($coach->getId());
        $dto->setNom($coach->getNom());
        $dto->setPrenom($coach->getPrenom());

        return $dto;
    }
}
