<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "groupe")]
#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe extends AbstractEntity
{

    #[ORM\Column]
    private ?int $taille = 0;

    #[ORM\Column(nullable: true)]
    private ?float $note = 0;

    /**
     * @var Collection<int, Etudiant>
     */
    #[ORM\OneToMany(targetEntity: Etudiant::class, mappedBy: 'groupe')]
    private Collection $etudiant;

    #[ORM\ManyToOne(inversedBy: 'groupes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Liste $liste = null;

    public function __construct()
    {
        $this->etudiant = new ArrayCollection();
    }

    public function getTaille(): ?int
    {
        return $this->taille;
    }

    public function setTaille(int $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): static
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection<int, Etudiant>
     */
    public function getEtudiant(): Collection
    {
        return $this->etudiant;
    }

    public function addEtudiant(Etudiant $etudiant): static
    {
        if (!$this->etudiant->contains($etudiant)) {
            $this->etudiant->add($etudiant);
            $etudiant->setGroupe($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): static
    {
        if ($this->etudiant->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getGroupe() === $this) {
                $etudiant->setGroupe(null);
            }
        }

        return $this;
    }

    public function getListe(): ?Liste
    {
        return $this->liste;
    }

    public function setListe(?Liste $liste): static
    {
        $this->liste = $liste;

        return $this;
    }

    
}
