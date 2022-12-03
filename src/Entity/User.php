<?php

namespace App\Entity;

use App\Entity\Groupe;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\State\UserStateProcessor;
use App\Repository\UserRepository;
use App\State\CurrentUserProvider;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\UserGroupController;
use MessageFormatter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]

// #[Post(processor: UserStateProcessor::class)]

#[ApiResource(
    formats: ['json'],
    operations: [

        // Route GET :  /users/{id}
        new Get(
            normalizationContext: ['groups' => ['get:user:IfRoleUser']],
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')",
            openapiContext: [
                'summary' => 'Retourne les informations d\'un utilisateur (nom prenom email groupe)', 
            ]
        ),

        // Route PATCH :  /users/{id}
        new Patch(
            denormalizationContext: ['groups' => ['user:patch:write']],
            normalizationContext: ['groups' => ['user:patch:read']],
            processor: UserStateProcessor::class,
            security: "is_granted('ROLE_ADMIN') or (object == user and previous_object == user)",
            securityMessage: "Vous n'avez pas les droits pour modifier cet utilisateur",
            openapiContext: [
                'summary' => 'Modifier un utilisateur', 
            ]
        ),

        // Route POST :  /users/
        new Post(
            denormalizationContext: ['groups' => ['user:post:write']],
            normalizationContext: ['groups' => ['user:post:returnData']],
            output: [],
            processor: UserStateProcessor::class,
            openapiContext: [
                'summary' => 'Inscrire un utilisateur', 
            ]
        ),

        // Route GET :  /users/
        new GetCollection(
            normalizationContext: ['groups' => ['user:getCollection:read']],
            openapiContext: [
                'summary' => 'Liste des utilisateurs (nom, prenom)', 
            ]
        ),

        // Route GET :  /admin/users/
        new GetCollection(
            uriTemplate: 'admin/users',
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Vous n'avez pas les droits pour consulter ces informations",
            openapiContext: [
                'summary' => 'Admin - Liste des utilisateurs', 
            ]
        ),

        // Route GET :  /users/me
        new Get(
            uriTemplate : '/me',
            provider: CurrentUserProvider::class,
            normalizationContext: ['groups' => ['user:get:read']],
            security: "is_granted('ROLE_USER')",
            openapiContext: [
                'summary' => 'Retoune les informations de l\'utilisateur connecté ', 
            ]
        ),

        // Route DELETE :  /users/{id}
        new Delete(
            security: "is_granted('ROLE_ADMIN') or (object == user and previous_object == user)",
            securityMessage: "Vous n'avez pas les droits pour supprimer cet utilisateur",
            openapiContext: [
                'summary' => 'Admin - Supprime un utilisateur', 
            ]
        ),

        // Route PUT :  /addUser/{id}/toGroup/{group}
        new Put(
            uriTemplate : '/addUser/{id}/toGroup/{group}',
            controller: UserGroupController::class,
            securityMessage: "Vous n'avez pas le droit d'ajouter un utilisateur à un groupe",
            read: false,
            openapiContext: [
                'summary' => 'Ajouter un utilisateur à un groupe', 
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object', 
                                'properties' => [ ]
                            ], 
                            'example' => [ ]
                        ]
                    ]
                ]
            ]
        ),
       

    ],
)]


class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['get:user:IfRoleUser','user:get:read', 'user:post:write', 'user:patch:write'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:post:write', 'user:patch:write'])]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get:user:IfRoleUser','user:getCollection:read', 'user:get:read', 'user:post:write', 'user:patch:write'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get:user:IfRoleUser','user:getCollection:read', 'user:get:read', 'user:post:write', 'user:patch:write'])]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['get:user:IfRoleUser' ,'user:get:read'])]
    private ?Groupe $groupe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }
}
