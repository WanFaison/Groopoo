<?php

namespace App\Entity;

use App\Repository\ListeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "liste")]
#[ORM\Entity(repositoryClass: ListeRepository::class)]
class Liste extends AbstractEntity
{

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'liste')]
    private Collection $groupes;

    #[ORM\ManyToOne(inversedBy: 'listes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Annee $annee = null;

    #[ORM\ManyToOne(inversedBy: 'listes')]
    private ?Ecole $ecole = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $critere = null;

    /**
     * @var Collection<int, Jour>
     */
    #[ORM\OneToMany(targetEntity: Jour::class, mappedBy: 'liste')]
    private Collection $jours;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
        $this->jours = new ArrayCollection();
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function setGroupes(?Collection $groupes): static
    {
        $this->groupes = $groupes;

        return $this;
    }

    public function addGroupe(Groupe $groupe): static
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->setListe($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getListe() === $this) {
                $groupe->setListe(null);
            }
        }

        return $this;
    }

    public function getAnnee(): ?Annee
    {
        return $this->annee;
    }

    public function setAnnee(?Annee $annee): static
    {
        $this->annee = $annee;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getCritere(): ?array
    {
        return $this->critere;
    }

    public function setCritere(?array $critere): static
    {
        $this->critere = $critere;

        return $this;
    }

    /**
     * @return Collection<int, Jour>
     */
    public function getJours(): Collection
    {
        return $this->jours;
    }

    public function addJour(Jour $jour): static
    {
        if (!$this->jours->contains($jour)) {
            $this->jours->add($jour);
            $jour->setListe($this);
        }

        return $this;
    }

    public function removeJour(Jour $jour): static
    {
        if ($this->jours->removeElement($jour)) {
            // set the owning side to null (unless already changed)
            if ($jour->getListe() === $this) {
                $jour->setListe(null);
            }
        }

        return $this;
    }
}
