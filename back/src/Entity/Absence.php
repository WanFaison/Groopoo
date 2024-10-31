<?php

namespace App\Entity;

use App\Enums\AbsenceType;
use App\Repository\AbsenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "absence")]
#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
class Absence extends AbstractEntity
{
    #[ORM\Column(nullable: true)]
    private ?AbsenceType $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbreHr = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    private ?Jour $jour = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    private ?Etudiant $etudiant = null;

    public function getNbreHr(): ?int
    {
        return $this->nbreHr;
    }

    public function setNbreHr(?int $nbreHr): static
    {
        $this->nbreHr = $nbreHr;

        return $this;
    }

    public function getJour(): ?Jour
    {
        return $this->jour;
    }

    public function setJour(?Jour $jour): static
    {
        $this->jour = $jour;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getType(): ?AbsenceType
    {
        return $this->type;
    }

    public function setType(?AbsenceType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
