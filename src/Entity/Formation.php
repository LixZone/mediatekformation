<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité Formation
 * 
 * Représente une formation vidéo associée à une playlist et à une ou plusieurs catégories.
 * Cette entité est persistée en base de données via Doctrine ORM.
 */
#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    /**
     * Début de chemin vers les images YouTube
     * 
     * Utilisé pour générer dynamiquement les URLs des miniatures et images HD
     */
    private const CHEMIN_IMAGE = 'https://i.ytimg.com/vi/';
    
    /**
     * Identifiant unique de la formation
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Date de publication de la formation
     * 
     * Doit être renseignée et ne peut pas être dans le futur
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Assert\NotBlank(message: "La date est obligatoire")]
    #[Assert\LessThanOrEqual('today', message: "La date ne peut pas être dans le futur")]
    private ?\DateTimeInterface $publishedAt = null;

    /**
     * Titre de la formation
     */
    #[ORM\Column(length: 100, nullable: false)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    private ?string $title = null;

    /**
     * Description détaillée de la formation
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * Identifiant de la vidéo YouTube
     * 
     * Sert à construire les URLs des images associées
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\NotBlank(message: "La video est obligatoire")]
    private ?string $videoId = null;

    /**
     * Playlist associée à la formation (relation ManyToOne)
     */
    #[ORM\ManyToOne(inversedBy: 'formations')]
    private ?Playlist $playlist = null;

    /**
     * Liste des catégories associées à la formation (relation ManyToMany)
     * 
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'formations')]
    private Collection $categories;

    /**
     * Constructeur
     * 
     * Initialise la collection de catégories
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant de la formation
     * 
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la date de publication
     * 
     * @return \DateTimeInterface|null
     */
    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * Définit la date de publication
     * 
     * @param \DateTimeInterface|null $publishedAt
     * @return static
     */
    public function setPublishedAt(?\DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
    
    /**
     * Retourne la date de publication formatée (d/m/Y)
     * 
     * @return string
     */
    public function getPublishedAtString(): string
    {
        if ($this->publishedAt === null) {
            return '';
        }
        return $this->publishedAt->format('d/m/Y');
    }

    /**
     * Retourne le titre
     * 
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre
     * 
     * @param string|null $title
     * @return static
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Retourne la description
     * 
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description
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
     * Retourne l'identifiant de la vidéo YouTube
     * 
     * @return string|null
     */
    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    /**
     * Définit l'identifiant de la vidéo YouTube
     * 
     * @param string|null $videoId
     * @return static
     */
    public function setVideoId(?string $videoId): static
    {
        $this->videoId = $videoId;

        return $this;
    }

    /**
     * Retourne l'URL de la miniature (petite image)
     * 
     * @return string|null
     */
    public function getMiniature(): ?string
    {
        return self::CHEMIN_IMAGE . $this->videoId . '/default.jpg';
    }

    /**
     * Retourne l'URL de l'image HD
     * 
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return self::CHEMIN_IMAGE . $this->videoId . '/hqdefault.jpg';
    }

    /**
     * Retourne la playlist associée
     * 
     * @return Playlist|null
     */
    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }

    /**
     * Définit la playlist associée
     * 
     * @param Playlist|null $playlist
     * @return static
     */
    public function setPlaylist(?Playlist $playlist): static
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * Retourne la liste des catégories
     * 
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * Ajoute une catégorie à la formation
     * 
     * @param Categorie $category
     * @return static
     */
    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * Supprime une catégorie de la formation
     * 
     * @param Categorie $category
     * @return static
     */
    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
