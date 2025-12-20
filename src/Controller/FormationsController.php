<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Form\FormationType;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Contrôleur des formations
 *
 * Gère l'affichage des formations (front et back office),
 * ainsi que les opérations CRUD (ajout, modification, suppression),
 * les tris et les recherches.
 */
class FormationsController extends AbstractController
{
    /**
     * Template utilisé pour le front office
     */
    private const FORMATIONS_TEMPLATE = 'pages/formations.html.twig';

    /**
     * Template utilisé pour le back office
     */
    private const ADMIN_FORMATIONS_TEMPLATE = 'admin/formations.html.twig';

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
     * Repository des playlists
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;

    /**
     * Constructeur avec injection des repositories
     *
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     * @param PlaylistRepository $playlistRepository
     */
    public function __construct(
        FormationRepository $formationRepository,
        CategorieRepository $categorieRepository,
        PlaylistRepository $playlistRepository
    ) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }

    /**
     * Affiche la liste des formations (front office)
     *
     * @return Response
     */
    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::FORMATIONS_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Affiche la liste des formations (back office)
     *
     * @return Response
     */
    #[Route('/admin/formations', name: 'admin.formations')]
    public function indexAdmin(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::ADMIN_FORMATIONS_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Trie les formations (front office)
     *
     * @param string $champ
     * @param string $ordre
     * @param string $table
     * @return Response
     */
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table = ''): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::FORMATIONS_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Trie les formations (back office)
     *
     * @param string $champ
     * @param string $ordre
     * @param string $table
     * @return Response
     */
    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sortAdmin($champ, $ordre, $table = ''): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::ADMIN_FORMATIONS_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Recherche des formations (front office)
     *
     * @param string $champ
     * @param Request $request
     * @param string $table
     * @return Response
     */
    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ''): Response
    {
        $valeur = $request->get('recherche');
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::FORMATIONS_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Recherche des formations (back office)
     *
     * @param string $champ
     * @param Request $request
     * @param string $table
     * @return Response
     */
    #[Route('/admin/formations/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
    public function findAllContainAdmin($champ, Request $request, $table = ''): Response
    {
        $valeur = $request->get('recherche');
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::ADMIN_FORMATIONS_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Affiche le détail d'une formation (front office)
     *
     * @param int $id
     * @return Response
     */
    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }
    
    /**
     * Affiche le détail d'une formation (back office)
     *
     * @param int $id
     * @return Response
     */
    #[Route('/admin/formations/formation/{id}', name: 'admin.formations.showone')]
    public function showOneAdmin($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("admin/formation.html.twig", [
            'formation' => $formation
        ]);
    }
    
    /**
     * Ajoute une nouvelle formation
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/admin/formations/add', name: 'admin.formations.add')]
    public function add(Request $request, EntityManagerInterface $em)
    {
        $formation = new Formation();

        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($formation);
            $em->flush();

            return $this->redirectToRoute('admin.formations.add');
        }

        return $this->render('admin/formation_add.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Modifie une formation existante
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     */
    #[Route('/admin/formations/edit/{id}', name: 'admin.formations.edit')]
    public function edit(Request $request, EntityManagerInterface $em, $id)
    {
        $formation = $em->getRepository(Formation::class)->find($id);
        
        if (!$formation){
            throw $this->createNotFoundException("Formation non trouvée");
        }

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            return $this->redirectToRoute('admin.formations');
        }
        
        return $this->render("admin/formation_edit.html.twig", [
            'form' => $form->createView(),
        ]);  
    }
    
    /**
     * Supprime une formation
     *
     * Supprime également le lien avec la playlist si nécessaire
     *
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     */
    #[Route('/admin/formations/remove/{id}', name: 'admin.formations.remove')]
    public function remove(EntityManagerInterface $em, $id): Response
    {
        $formation = $em->getRepository(Formation::class)->find($id);
        
        if (!$formation) {
            throw $this->createNotFoundException("Formation non trouvée");
        }
        
        // Supprime la relation avec la playlist si elle existe
        $playlist = $formation->getPlaylist();

        if ($playlist) {
            $playlist->removeFormation($formation);
        }
        
        $em->remove($formation);
        $em->flush();

        return $this->redirectToRoute('admin.formations');
    }
}
