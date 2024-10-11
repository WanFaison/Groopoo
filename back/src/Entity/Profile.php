<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use App\Enums\Authorization;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "profile")]
#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile extends AbstractEntity
{
    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'profile')]
    private Collection $users;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $authorizations = [];

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->authorizations = [];
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addProfile($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeProfile($this);
        }

        return $this;
    }

    /**
     * Get all authorizations as enum instances.
     * 
     * @return Authorization[]
     */
    public function getAuthorizations(): array
    {
        return array_map(
            fn (string $authorization) => Authorization::from($authorization),
            $this->authorizations
        );
    }

    public function addAuthorization(Authorization $authorization): self
    {
        $this->authorizations[] = $authorization->value;
        return $this;
    }

    public function removeAuthorization(Authorization $authorization): self
    {
        $this->authorizations = array_filter($this->authorizations, fn (string $s) => $s !== $authorization->value);
        return $this;
    }
}

