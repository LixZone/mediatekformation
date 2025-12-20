<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository Categorie
 * 
 * Fournit les méthodes d'accès aux données pour l'entité Categorie.
 * Permet d'interagir avec la base de données via Doctrine ORM.
 * 
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Categorie::class);
    }

    /**
     * Persiste une catégorie en base de données
     * 
     * Sauvegarde immédiatement l'entité (persist + flush)
     *
     * @param Categorie $entity
     * @return void
     */
    public function add(Categorie $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Supprime une catégorie de la base de données
     * 
     * Suppression immédiate (remove + flush)
     *
     * @param Categorie $entity
     * @return void
     */
    public function remove(Categorie $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne la liste des catégories associées aux formations d'une playlist
     * 
     * Effectue une jointure entre :
     * - les catégories
     * - les formations
     * - la playlist
     * 
     * Résultat trié par nom de catégorie (ordre alphabétique)
     *
     * @param int $idPlaylist Identifiant de la playlist
     * @return array<Categorie> Liste des catégories correspondantes
     */
    public function findAllForOnePlaylist($idPlaylist): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.formations', 'f')
            ->join('f.playlist', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $idPlaylist)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
