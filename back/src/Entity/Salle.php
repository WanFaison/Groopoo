<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "salle")]
#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle extends AbstractEntity
{
    #[ORM\ManyToOne(inversedBy: 'salles')]
    private ?Etage $etage = null;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'salle')]
    private Collection $groupes;

    #[ORM\ManyToOne(inversedBy: 'salles')]
    private ?Ecole $ecole = null;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
    }

    public function getEtage(): ?Etage
    {
        return $this->etage;
    }

    public function setEtage(?Etage $etage): static
    {
        $this->etage = $etage;

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): static
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->setSalle($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getSalle() === $this) {
                $groupe->setSalle(null);
            }
        }

        return $this;
    }

    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    public function setEcole(?Ecole $ecole): static
    {
        $this->ecole = $ecole;

        return $this;
    }
}
