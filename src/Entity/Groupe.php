<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\State\GroupeStateProcessor;
use App\Repository\GroupeRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\GroupUserController;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
#[ApiResource(
    formats: ['json'],
    operations: [

        // Route GET : /groupes/
        new GetCollection(
            normalizationContext: ['groups' => ['user:getCollection:read']],
            openapiContext: [
                'summary' => 'Liste des groupes', 
            ]

        ),

        // Route GET : /groups/users
        new GetCollection(
            uriTemplate : '/groups/users',
            controller: GroupUserController::class,
            normalizationContext: ['groups' => ['userByGroups:get:read']],
            openapiContext: [
                'summary' => 'Retourne les groupes et leurs utilisateurs', 
            ]
        ),

        // Route GET : /groupes/{id}
        new Get(
            normalizationContext: ['groups' => ['group:get:read']],
            openapiContext: [
                'summary' => 'Retourne les informations d\'un groupe', 
            ]
        ),

        // Route PUT : /groupes/{id}
        new Put(
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['group:put:write']],
            normalizationContext: ['groups' => ['group:put:read']],
            processor: GroupeStateProcessor::class,
            openapiContext: [
                'summary' => 'Admin - Modifier un groupe', 
            ]
        ),
        
        // Route POST : /groupes/
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            processor: GroupeStateProcessor::class,
            denormalizationContext: ['groups' => ['group:post:read']],
            normalizationContext: ['groups' => ['group:get:read']],
            openapiContext: [
                'summary' => 'Admin - Ajouter les groupes', 
            ]
        ),
        // Route DELETE : /groupes/{id}
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'summary' => 'Admin - Supprimer un groupe', 
            ]

        )

    ],
        
)]


class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:getCollection:read', 'group:get:read', 'userByGroups:get:read', 'group:post:read','group:put:write'])]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Groups([ 'userByGroups:get:read'])]
    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setGroupe($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGroupe() === $this) {
                $user->setGroupe(null);
            }
        }

        return $this;
    }
}
