<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository Formation
 * 
 * Fournit les méthodes d'accès aux données pour l'entité Formation.
 * Permet d'effectuer des requêtes personnalisées via Doctrine ORM.
 * 
 * @extends ServiceEntityRepository<Formation>
 */
class FormationRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Formation::class);
    }

    /**
     * Persiste une formation en base de données
     * 
     * Sauvegarde immédiate (persist + flush)
     *
     * @param Formation $entity
     * @return void
     */
    public function add(Formation $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Supprime une formation de la base de données
     * 
     * Suppression immédiate (remove + flush)
     *
     * @param Formation $entity
     * @return void
     */
    public function remove(Formation $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne toutes les formations triées selon un champ donné
     * 
     * Possibilité de trier sur un champ de l'entité Formation
     * ou d'une entité liée (via jointure)
     *
     * @param string $champ Nom du champ à utiliser pour le tri
     * @param string $ordre Sens du tri (ASC ou DESC)
     * @param string $table Nom de la relation si le champ appartient à une autre table
     * @return Formation[]
     */
    public function findAllOrderBy(string $champ, string $ordre, string $table = ''): array
    {
        if ($table === '') {
            return $this->createQueryBuilder('f')
                ->orderBy('f.' . $champ, $ordre)
                ->getQuery()
                ->getResult();
        }

        return $this->createQueryBuilder('f')
            ->join('f.' . $table, 't')
            ->orderBy('t.' . $champ, $ordre)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des formations dont un champ contient une valeur donnée
     * 
     * Si la valeur est vide, retourne toutes les formations
     * Possibilité de filtrer sur un champ de l'entité ou d'une relation
     *
     * @param string $champ Nom du champ à filtrer
     * @param string $valeur Valeur recherchée (LIKE %valeur%)
     * @param string $table Nom de la relation si le champ appartient à une autre table
     * @return Formation[]
     */
    public function findByContainValue(string $champ, string $valeur, string $table = ''): array
    {
        if ($valeur === '') {
            return $this->findAll();
        }

        if ($table === '') {
            return $this->createQueryBuilder('f')
                ->where('f.' . $champ . ' LIKE :valeur')
                ->orderBy('f.publishedAt', 'DESC')
                ->setParameter('valeur', '%' . $valeur . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->createQueryBuilder('f')
            ->join('f.' . $table, 't')
            ->where('t.' . $champ . ' LIKE :valeur')
            ->orderBy('f.publishedAt', 'DESC')
            ->setParameter('valeur', '%' . $valeur . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les N formations les plus récentes
     * 
     * Triées par date de publication décroissante
     *
     * @param int $nb Nombre maximum de résultats
     * @return Formation[]
     */
    public function findAllLasted(int $nb): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.publishedAt', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la liste des formations associées à une playlist
     * 
     * Résultats triés par date de publication croissante
     *
     * @param int $idPlaylist Identifiant de la playlist
     * @return Formation[]
     */
    public function findAllForOnePlaylist(int $idPlaylist): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.playlist', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $idPlaylist)
            ->orderBy('f.publishedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
