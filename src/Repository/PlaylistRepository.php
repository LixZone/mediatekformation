<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository Playlist
 * 
 * Fournit les méthodes d'accès aux données pour l'entité Playlist.
 * Permet d'effectuer des requêtes personnalisées via Doctrine ORM.
 * 
 * @extends ServiceEntityRepository<Playlist>
 */
class PlaylistRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Playlist::class);
    }

    /**
     * Persiste une playlist en base de données
     * 
     * Sauvegarde immédiate (persist + flush)
     *
     * @param Playlist $entity
     * @return void
     */
    public function add(Playlist $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Supprime une playlist de la base de données
     * 
     * Suppression immédiate (remove + flush)
     *
     * @param Playlist $entity
     * @return void
     */
    public function remove(Playlist $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne toutes les playlists triées par leur nom
     * 
     * Utilise une jointure avec les formations pour éviter les doublons
     * grâce au groupBy
     *
     * @param string $ordre Sens du tri (ASC ou DESC)
     * @return Playlist[]
     */
    public function findAllOrderByName(string $ordre): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.formations', 'f')
            ->groupBy('p.id')
            ->orderBy('p.name', $ordre)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des playlists dont un champ contient une valeur donnée
     * 
     * Si la valeur est vide, retourne toutes les playlists triées par nom
     * Possibilité de filtrer :
     * - sur un champ de Playlist
     * - ou sur un champ d'une relation (ex: catégories via formations)
     *
     * @param string $champ Nom du champ à filtrer
     * @param string $valeur Valeur recherchée (LIKE %valeur%)
     * @param string $table Nom de la relation si le champ appartient à une autre table
     * @return Playlist[]
     */
    public function findByContainValue(string $champ, string $valeur, string $table = ''): array
    {
        if ($valeur === '') {
            return $this->findAllOrderByName('ASC');
        }

        if ($table === '') {
            return $this->createQueryBuilder('p')
                ->leftJoin('p.formations', 'f')
                ->where('p.' . $champ . ' LIKE :valeur')
                ->setParameter('valeur', '%' . $valeur . '%')
                ->groupBy('p.id')
                ->orderBy('p.name', 'ASC')
                ->getQuery()
                ->getResult();
        }

        return $this->createQueryBuilder('p')
            ->leftJoin('p.formations', 'f')
            ->leftJoin('f.categories', 'c')
            ->where('c.' . $champ . ' LIKE :valeur')
            ->setParameter('valeur', '%' . $valeur . '%')
            ->groupBy('p.id')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Retourne les playlists associées à une formation donnée
     * 
     * Permet de récupérer les playlists contenant une formation spécifique
     *
     * @param int $idFormation Identifiant de la formation
     * @return Playlist[]
     */
    public function findAllForOneFormation(int $idFormation): array
    {
            return $this->createQueryBuilder('p')
                ->join('p.formations', 'f')
                ->where('f.id = :id')
                ->setParameter('id', $idFormation)
                ->orderBy('f.publishedAt', 'ASC')
                ->getQuery()
                ->getResult();
    }
}
