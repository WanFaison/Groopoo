<?php

namespace App\Entity;

use App\Repository\EtageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "etage")]
#[ORM\Entity(repositoryClass: EtageRepository::class)]
class Etage extends AbstractEntity
{
    #[ORM\ManyToOne(inversedBy: 'etages')]
    private ?Ecole $ecole = null;

    /**
     * @var Collection<int, Salle>
     */
    #[ORM\OneToMany(targetEntity: Salle::class, mappedBy: 'etage')]
    private Collection $salles;

    public function __construct()
    {
        $this->salles = new ArrayCollection();
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

    /**
     * @return Collection<int, Salle>
     */
    public function getSalles(): Collection
    {
        return $this->salles;
    }

    public function addSalle(Salle $salle): static
    {
        if (!$this->salles->contains($salle)) {
            $this->salles->add($salle);
            $salle->setEtage($this);
        }

        return $this;
    }

    public function removeSalle(Salle $salle): static
    {
        if ($this->salles->removeElement($salle)) {
            // set the owning side to null (unless already changed)
            if ($salle->getEtage() === $this) {
                $salle->setEtage(null);
            }
        }

        return $this;
    }
}
