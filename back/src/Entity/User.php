<?php

namespace App\Entity;

use App\Enums\Role;
use App\Enums\Etat;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $username = null;

    #[ORM\Column(nullable: true)]
    private ?Etat $etat = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = 'xx-xxx-xx-xx';

    /**
     * @var list<Role> The user roles
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 60, nullable: true, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $isArchived = null;

    /**
     * @var Collection<int, Ecole>
     */
    #[ORM\ManyToMany(targetEntity: Ecole::class, inversedBy: 'admins')]
    private Collection $ecoles;

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

    /**
     * @var Collection<int, Liste>
     */
    #[ORM\ManyToMany(targetEntity: Liste::class, inversedBy: 'coachs')]
    private Collection $listes;

    public function __construct()
    {
        $this->ecoles = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->jury = new ArrayCollection();
        $this->listes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @return list<Role> The user roles as Role enum
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param list<Role> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Create a User from JWT payload.
     *
     * @param string $username
     * @param array $payload
     * @return static
     */
    public static function createFromPayload($username, array $payload): self
    {
        $user = new self();
        $user->username = $username;

        // Optionally extract more data from the payload and set it
        // For example, if you store roles or other custom claims in the JWT:
        if (isset($payload['roles'])) {
            $user->roles = $payload['roles'];
        }

        return $user;
    }

    /**
     * @return Collection<int, Ecole>
     */
    public function getEcoles(): Collection
    {
        return $this->ecoles;
    }

    public function addEcole(Ecole $ecole): static
    {
        if (!$this->ecoles->contains($ecole)) {
            $this->ecoles->add($ecole);
        }

        return $this;
    }

    public function removeEcole(Ecole $ecole): static
    {
        $this->ecoles->removeElement($ecole);

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
            $liste->addCoach($this);
        }

        return $this;
    }

    public function removeListe(Liste $liste): static
    {
        $this->listes->removeElement($liste);

        return $this;
    }
}
