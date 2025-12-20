<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Entité User
 * 
 * Représente un utilisateur de l'application.
 * Implémente les interfaces de sécurité Symfony pour la gestion de l'authentification.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique de l'utilisateur
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom d'utilisateur unique
     * 
     * Utilisé comme identifiant de connexion
     */
    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * Rôles de l'utilisateur
     * 
     * Exemple : ROLE_USER, ROLE_ADMIN
     * 
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe hashé
     * 
     * Stocke uniquement une version sécurisée du mot de passe
     * 
     * @var string
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Retourne l'identifiant de l'utilisateur
     * 
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom d'utilisateur
     * 
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Définit le nom d'utilisateur
     * 
     * @param string $username
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Retourne un identifiant visuel unique pour l'utilisateur
     * 
     * Utilisé par le système de sécurité Symfony
     * 
     * @see UserInterface
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * Retourne les rôles de l'utilisateur
     * 
     * Garantit que chaque utilisateur possède au minimum le rôle ROLE_USER
     * 
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // Garantit que chaque utilisateur a au moins ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur
     * 
     * @param list<string> $roles
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Retourne le mot de passe hashé
     * 
     * Utilisé par le système de sécurité Symfony
     * 
     * @see PasswordAuthenticatedUserInterface
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe hashé
     * 
     * @param string $password
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Supprime les données sensibles temporaires
     * 
     * Méthode appelée automatiquement par Symfony après authentification
     * 
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials(): void
    {
        // Si des données sensibles temporaires sont stockées (ex: plainPassword),
        // elles doivent être nettoyées ici
        // $this->plainPassword = null;
    }
}
