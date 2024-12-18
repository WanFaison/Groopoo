<?php

namespace App\Entity;

use App\Repository\JuryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "jury")]
#[ORM\Entity(repositoryClass: JuryRepository::class)]
class Jury extends AbstractEntity
{
    #[ORM\ManyToOne(inversedBy: 'juries')]
    private ?Liste $liste = null;

    #[ORM\Column(nullable: true)]
    private ?int $effectif = null;

    /**
     * @var Collection<int, Coach>
     */
    #[ORM\ManyToMany(targetEntity: Coach::class, mappedBy: 'jury')]
    private Collection $coaches;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'jury')]
    private Collection $groupes;

    #[ORM\Column]
    private ?bool $isFinal = null;

    public function __construct()
    {
        $this->coaches = new ArrayCollection();
        $this->groupes = new ArrayCollection();
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

    public function getEffectif(): ?int
    {
        return $this->effectif;
    }

    public function setEffectif(?int $effectif): static
    {
        $this->effectif = $effectif;

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
            $coach->addJury($this);
        }

        return $this;
    }

    public function removeCoach(Coach $coach): static
    {
        if ($this->coaches->removeElement($coach)) {
            $coach->removeJury($this);
        }

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
            $groupe->setJury($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getJury() === $this) {
                $groupe->setJury(null);
            }
        }

        return $this;
    }

    public function isFinal(): ?bool
    {
        return $this->isFinal;
    }

    public function setFinal(bool $isFinal): static
    {
        $this->isFinal = $isFinal;

        return $this;
    }
}
