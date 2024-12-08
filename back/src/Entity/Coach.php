<?php

namespace App\Entity;

use App\Enums\Etat;
use App\Repository\CoachRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "coach")]
#[ORM\Entity(repositoryClass: CoachRepository::class)]
class Coach extends AbstractEntity
{
    #[ORM\Column(nullable: true)]
    private ?Etat $etat = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $email = null;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'coach')]
    private Collection $groupes;

    /**
     * @var Collection<int, Jury>
     */
    #[ORM\ManyToMany(targetEntity: Jury::class, inversedBy: 'coaches')]
    private Collection $jury;

    #[ORM\ManyToOne(inversedBy: 'coaches')]
    private ?Ecole $ecole = null;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
        $this->jury = new ArrayCollection();
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): static
    {
        $this->etat = $etat;

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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

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
            $groupe->setCoach($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getCoach() === $this) {
                $groupe->setCoach(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Jury>
     */
    public function getJury(): Collection
    {
        return $this->jury;
    }

    public function addJury(Jury $jury): static
    {
        if (!$this->jury->contains($jury)) {
            foreach($this->jury as $j){ $j->getListe() == $jury->getListe() ? 
                                        $this->removeJury($j) : null; }
            $this->jury->add($jury);
        }

        return $this;
    }

    public function removeJury(Jury $jury): static
    {
        $this->jury->removeElement($jury);

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
