<?php

namespace App\Controller\Dto\Response;

use App\Controller\Dto\RestResponse;
use App\Entity\Annee;
use App\Entity\Coach;
use App\Entity\Etage;
use App\Entity\Salle;
use App\Repository\AnneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoachResponseDto
{
    private int $id;
    private string $nom;
    private string $prenom;
    private string $tel;
    private string $email;
    private string $etat;
    private string $ecole;
    private int $ecoleId;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getEcole(): ?string
    {
        return $this->ecole;
    }

    public function setEcole(string $ecole): static
    {
        $this->ecole = $ecole;

        return $this;
    }

    public function getEcoleId(): ?int
    {
        return $this->ecoleId;
    }
    public function setEcoleId(int $ecoleId): static
    {
        $this->ecoleId = $ecoleId;
        return $this;
    }

    public function toDto(Coach $coach): CoachResponseDto
    {
        $dto = new CoachResponseDto;

        $dto->setId($coach->getId());
        $dto->setNom($coach->getNom());
        $dto->setPrenom($coach->getPrenom());
        $dto->setTel($coach->getTelephone());
        $dto->setEmail($coach->getEmail());
        $dto->setEtat($coach->getEtat()->value);
        $dto->setEcole($coach->getEcole()->getLibelle());
        $dto->setEcoleId($coach->getEcole()->getId());

        return $dto;
    }
}
