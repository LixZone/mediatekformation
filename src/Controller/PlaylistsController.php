<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Contrôleur des playlists
 *
 * Gère l'affichage des playlists (front et back office),
 * ainsi que les opérations CRUD, les tris et les recherches.
 */
class PlaylistsController extends AbstractController
{
    /**
     * Template du front office
     */
    private const PLAYLISTS_TEMPLATE = 'pages/playlists.html.twig';

    /**
     * Template du back office
     */
    private const ADMIN_PLAYLISTS_TEMPLATE = 'admin/playlists.html.twig';

    /**
     * Repository des playlists
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;

    /**
     * Repository des formations
     *
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     * Repository des catégories
     *
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     * Constructeur avec injection des repositories
     *
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     * @param FormationRepository $formationRespository
     */
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRespository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }

    /**
     * Affiche la liste des playlists (front office)
     *
     * Ajoute le nombre de formations pour chaque playlist
     *
     * @return Response
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        $playlistsWithformationsCount = [];

        // Ajoute le nombre de formations pour chaque playlist
        for ($i = 0; $i < count($playlists); $i++) {
            $playlistsWithformationsCount[] = [
                'playlist' => $playlists[$i],
                'formationsCount' => count($playlists[$i]->getFormations())
            ];
        }
        
        return $this->render(self::PLAYLISTS_TEMPLATE, [
            'playlists' => $playlistsWithformationsCount,
            'categories' => $categories,
        ]);
    }
    
    /**
     * Affiche la liste des playlists (back office)
     *
     * @return Response
     */
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function indexAdmin(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        $playlistsWithformationsCount = [];

        for ($i = 0; $i < count($playlists); $i++) {
            $playlistsWithformationsCount[] = [
                'playlist' => $playlists[$i],
                'formationsCount' => count($playlists[$i]->getFormations())
            ];
        }
        
        return $this->render(self::ADMIN_PLAYLISTS_TEMPLATE, [
            'playlists' => $playlistsWithformationsCount,
            'categories' => $categories,
        ]);
    }

    /**
     * Trie les playlists (front office)
     *
     * @param string $champ
     * @param string $ordre
     * @return Response
     */
    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort($champ, $ordre): Response
    {
        // Tri par nom
        if ($champ === "name") {
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        } else {
            $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        }

        $categories = $this->categorieRepository->findAll();
        $playlistsWithformationsCount = [];

        for ($i = 0; $i < count($playlists); $i++) {
            $playlistsWithformationsCount[] = [
                'playlist' => $playlists[$i],
                'formationsCount' => count($playlists[$i]->getFormations())
            ];
        }

        // Tri par nombre de formations
        if ($champ === "formationsCount") {
            usort($playlistsWithformationsCount, function ($a, $b) use ($ordre) {
                if ($ordre === 'ASC') {
                    return $a['formationsCount'] <=> $b['formationsCount'];
                } else {
                    return $b['formationsCount'] <=> $a['formationsCount'];
                }
            });
        }

        return $this->render(self::PLAYLISTS_TEMPLATE, [
            'playlists' => $playlistsWithformationsCount,
            'categories' => $categories
        ]);
    }
    
    /**
     * Trie les playlists (back office)
     */
    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sortAdmin($champ, $ordre): Response
    {
        if ($champ === "name") {
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        } else {
            $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        }

        $categories = $this->categorieRepository->findAll();
        $playlistsWithformationsCount = [];

        for ($i = 0; $i < count($playlists); $i++) {
            $playlistsWithformationsCount[] = [
                'playlist' => $playlists[$i],
                'formationsCount' => count($playlists[$i]->getFormations())
            ];
        }

        if ($champ === "formationsCount") {
            usort($playlistsWithformationsCount, function ($a, $b) use ($ordre) {
                return $ordre === 'ASC'
                    ? $a['formationsCount'] <=> $b['formationsCount']
                    : $b['formationsCount'] <=> $a['formationsCount'];
            });
        }

        return $this->render(self::ADMIN_PLAYLISTS_TEMPLATE, [
            'playlists' => $playlistsWithformationsCount,
            'categories' => $categories
        ]);
    }

    /**
     * Recherche de playlists (front office)
     */
    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ''): Response
    {
        $valeur = $request->get('recherche');
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        $playlistsWithformationsCount = [];

        for ($i = 0; $i < count($playlists); $i++) {
            $playlistsWithformationsCount[] = [
                'playlist' => $playlists[$i],
                'formationsCount' => count($playlists[$i]->getFormations())
            ];
        }

        return $this->render(self::PLAYLISTS_TEMPLATE, [
            'playlists' => $playlistsWithformationsCount,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Recherche de playlists (back office)
     */
    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContainAdmin($champ, Request $request, $table = ''): Response
    {
        $valeur = $request->get('recherche');
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        $playlistsWithformationsCount = [];

        for ($i = 0; $i < count($playlists); $i++) {
            $playlistsWithformationsCount[] = [
                'playlist' => $playlists[$i],
                'formationsCount' => count($playlists[$i]->getFormations())
            ];
        }

        return $this->render(self::ADMIN_PLAYLISTS_TEMPLATE, [
            'playlists' => $playlistsWithformationsCount,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Affiche le détail d'une playlist (front office)
     */
    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);

        return $this->render('pages/playlist.html.twig', [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
    
    /**
     * Affiche le détail d'une playlist (back office)
     */
    #[Route('/admin/playlists/playlist/{id}', name: 'admin.playlists.showone')]
    public function showOneAdmin($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);

        return $this->render('admin/playlist.html.twig', [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
    
    /**
     * Ajoute une playlist
     */
    #[Route('/admin/playlists/add', name: 'admin.playlists.add')]
    public function add(Request $request, EntityManagerInterface $em)
    {
        $playlist = new Playlist();

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($playlist);
            $em->flush();

            return $this->redirectToRoute('admin.playlists.add');
        }

        return $this->render('admin/playlist_add.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Modifie une playlist
     */
    #[Route('/admin/playlists/edit/{id}', name: 'admin.playlists.edit')]
    public function edit(Request $request, EntityManagerInterface $em, $id)
    {
        $playlist = $em->getRepository(Playlist::class)->find($id);

        if (!$playlist) {
            throw $this->createNotFoundException("Playlist non trouvée");
        }

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('admin.playlists');
        }

        return $this->render('admin/playlist_edit.html.twig', [
            'form' => $form->createView(),
            'playlist' => $playlist
        ]);
    }
    
    /**
     * Supprime une playlist
     *
     * Impossible si des formations sont liées
     */
    #[Route('/admin/playlists/remove/{id}', name: 'admin.playlists.remove')]
    public function remove(EntityManagerInterface $em, $id): Response
    {
        $playlist = $em->getRepository(Playlist::class)->find($id);

        if (!$playlist) {
            throw $this->createNotFoundException("Playlist non trouvée");
        }

        if (count($playlist->getFormations()) > 0) {
            $this->addFlash('error', 'Impossible de supprimer : des formations sont liées à cette playlist');
            return $this->redirectToRoute('admin.playlists');
        }
        
        $em->remove($playlist);
        $em->flush();

        return $this->redirectToRoute('admin.playlists');
    }
}
