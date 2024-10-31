<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "etudiant")]
#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
class Etudiant extends AbstractEntity
{

    #[ORM\Column(length: 255)]
    private ?string $matricule = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $prenom = null;

    #[ORM\ManyToOne(inversedBy: 'etudiant')]
    private ?Groupe $groupe = null;

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Classe $classe = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $nationalite = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $sexe = null;

    /**
     * @var Collection<int, Absence>
     */
    #[ORM\OneToMany(targetEntity: Absence::class, mappedBy: 'etudiant')]
    private Collection $absences;

    #[ORM\Column(nullable: true)]
    private ?float $noteEtd = null;

    #[ORM\Column(nullable: true)]
    private ?float $noteFinal = null;

    public function __construct()
    {
        $this->absences = new ArrayCollection();
    }


    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): static
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): static
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;
        $classe->addEtudiant($this);

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): static
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * @return Collection<int, Absence>
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(Absence $absence): static
    {
        if (!$this->absences->contains($absence)) {
            $this->absences->add($absence);
            $absence->setEtudiant($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): static
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getEtudiant() === $this) {
                $absence->setEtudiant(null);
            }
        }

        return $this;
    }

    public function getNoteEtd(): ?float
    {
        return $this->noteEtd;
    }

    public function setNoteEtd(?float $noteEtd): static
    {
        $this->noteEtd = $noteEtd;

        return $this;
    }

    public function getNoteFinal(): ?float
    {
        return $this->noteFinal;
    }

    public function setNoteFinal(?float $noteFinal): static
    {
        $this->noteFinal = $noteFinal;

        return $this;
    }
}
