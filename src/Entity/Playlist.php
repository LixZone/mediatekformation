<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité Playlist
 * 
 * Représente une playlist contenant plusieurs formations.
 * Une playlist permet d'organiser des formations par thème ou parcours.
 */
#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    /**
     * Identifiant unique de la playlist
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la playlist
     */
    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    private ?string $name = null;

    /**
     * Description de la playlist
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * Liste des formations associées à la playlist (relation OneToMany)
     * 
     * @var Collection<int, Formation>
     */
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'playlist')]
    private Collection $formations;

    /**
     * Constructeur
     * 
     * Initialise la collection de formations
     */
    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant de la playlist
     * 
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom de la playlist
     * 
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la playlist
     * 
     * @param string|null $name
     * @return static
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retourne la description de la playlist
     * 
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la playlist
     * 
     * @param string|null $description
     * @return static
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Retourne la liste des formations associées
     * 
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    /**
     * Ajoute une formation à la playlist
     * 
     * Met également à jour la relation côté Formation
     * 
     * @param Formation $formation
     * @return static
     */
    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setPlaylist($this);
        }

        return $this;
    }

    /**
     * Supprime une formation de la playlist
     * 
     * Met également à jour la relation côté Formation si nécessaire
     * 
     * @param Formation $formation
     * @return static
     */
    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation) && $formation->getPlaylist() === $this) {
            $formation->setPlaylist(null);
        }

        return $this;
    }

    /**
     * Retourne la liste des noms des catégories associées aux formations de la playlist
     * 
     * Cette méthode parcourt toutes les formations et extrait les catégories uniques
     * 
     * @return Collection<int, string>
     */
    public function getCategoriesPlaylist(): Collection
    {
        $categories = new ArrayCollection();

        foreach ($this->formations as $formation) {
            $categoriesFormation = $formation->getCategories();
            foreach ($categoriesFormation as $categorieFormation) {
                if (!$categories->contains($categorieFormation->getName())) {
                    $categories[] = $categorieFormation->getName();
                }
            }
        }

        return $categories;
    }
}
