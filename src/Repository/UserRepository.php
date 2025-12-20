<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Repository User
 * 
 * Fournit les méthodes d'accès aux données pour l'entité User.
 * Intègre également la gestion de la mise à jour automatique des mots de passe.
 * 
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Constructeur
     * 
     * Initialise le repository avec le gestionnaire Doctrine
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Met à jour automatiquement le mot de passe hashé d'un utilisateur
     * 
     * Utilisé par Symfony pour rehasher les mots de passe lorsque l'algorithme évolue
     *
     * @param PasswordAuthenticatedUserInterface $user Utilisateur concerné
     * @param string $newHashedPassword Nouveau mot de passe hashé
     * @return void
     * 
     * @throws UnsupportedUserException Si l'objet utilisateur n'est pas une instance de User
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
