<?php

namespace App\Entity;

use App\Repository\EcoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "ecole")]
#[ORM\Entity(repositoryClass: EcoleRepository::class)]
class Ecole extends AbstractEntity
{
    /**
     * @var Collection<int, Liste>
     */
    #[ORM\OneToMany(targetEntity: Liste::class, mappedBy: 'ecole')]
    private Collection $listes;

    /**
     * @var Collection<int, Filiere>
     */
    #[ORM\OneToMany(targetEntity: Filiere::class, mappedBy: 'ecole')]
    private Collection $filieres;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\OneToMany(targetEntity: Classe::class, mappedBy: 'ecole')]
    private Collection $classes;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'ecoles')]
    private Collection $admins;

    /**
     * @var Collection<int, Etage>
     */
    #[ORM\OneToMany(targetEntity: Etage::class, mappedBy: 'ecole')]
    private Collection $etages;

    /**
     * @var Collection<int, Coach>
     */
    #[ORM\OneToMany(targetEntity: Coach::class, mappedBy: 'ecole')]
    private Collection $coaches;

    /**
     * @var Collection<int, Salle>
     */
    #[ORM\OneToMany(targetEntity: Salle::class, mappedBy: 'ecole')]
    private Collection $salles;

    public function __construct()
    {
        $this->listes = new ArrayCollection();
        $this->filieres = new ArrayCollection();
        $this->classes = new ArrayCollection();
        $this->admins = new ArrayCollection();
        $this->etages = new ArrayCollection();
        $this->coaches = new ArrayCollection();
        $this->salles = new ArrayCollection();
    }

    /**
     * @return Collection<int, Liste>
     */
    public function getListes(): Collection
    {
        return $this->listes;
    }

    public function addListe(Liste $liste): static
    {
        if (!$this->listes->contains($liste)) {
            $this->listes->add($liste);
            $liste->setEcole($this);
        }

        return $this;
    }

    public function removeListe(Liste $liste): static
    {
        if ($this->listes->removeElement($liste)) {
            // set the owning side to null (unless already changed)
            if ($liste->getEcole() === $this) {
                $liste->setEcole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Filiere>
     */
    public function getFilieres(): Collection
    {
        return $this->filieres;
    }

    public function addFiliere(Filiere $filiere): static
    {
        if (!$this->filieres->contains($filiere)) {
            $this->filieres->add($filiere);
            $filiere->setEcole($this);
        }

        return $this;
    }

    public function removeFiliere(Filiere $filiere): static
    {
        if ($this->filieres->removeElement($filiere)) {
            // set the owning side to null (unless already changed)
            if ($filiere->getEcole() === $this) {
                $filiere->setEcole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->setEcole($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getEcole() === $this) {
                $class->setEcole(null);
            }
        }

        return $this;
    }
    

    /**
     * @return Collection<int, User>
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(User $admin): static
    {
        if (!$this->admins->contains($admin)) {
            $this->admins->add($admin);
            $admin->addEcole($this);
        }

        return $this;
    }

    public function removeAdmin(User $admin): static
    {
        if ($this->admins->removeElement($admin)) {
            $admin->removeEcole($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Etage>
     */
    public function getEtages(): Collection
    {
        return $this->etages;
    }

    public function addEtage(Etage $etage): static
    {
        if (!$this->etages->contains($etage)) {
            $this->etages->add($etage);
            $etage->setEcole($this);
        }

        return $this;
    }

    public function removeEtage(Etage $etage): static
    {
        if ($this->etages->removeElement($etage)) {
            // set the owning side to null (unless already changed)
            if ($etage->getEcole() === $this) {
                $etage->setEcole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Coach>
     */
    public function getCoaches(): Collection
    {
        return $this->coaches;
    }

    public function addCoach(Coach $coach): static
    {
        if (!$this->coaches->contains($coach)) {
            $this->coaches->add($coach);
            $coach->setEcole($this);
        }

        return $this;
    }

    public function removeCoach(Coach $coach): static
    {
        if ($this->coaches->removeElement($coach)) {
            // set the owning side to null (unless already changed)
            if ($coach->getEcole() === $this) {
                $coach->setEcole(null);
            }
        }

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
            $salle->setEcole($this);
        }

        return $this;
    }

    public function removeSalle(Salle $salle): static
    {
        if ($this->salles->removeElement($salle)) {
            // set the owning side to null (unless already changed)
            if ($salle->getEcole() === $this) {
                $salle->setEcole(null);
            }
        }

        return $this;
    }
}
