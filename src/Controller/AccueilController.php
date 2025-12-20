<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant les pages du front office (accueil et CGU)
 *
 * Permet d'afficher les dernières formations ainsi que les pages statiques.
 */
class AccueilController extends AbstractController
{
    /**
     * Repository permettant d'accéder aux données des formations
     *
     * @var FormationRepository
     */
    private $repository;

    /**
     * Constructeur du contrôleur
     *
     * Injection du repository des formations
     *
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Affiche la page d'accueil
     *
     * Récupère les dernières formations et les envoie à la vue
     *
     * @return Response
     */
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        $formations = $this->repository->findAllLasted(2);

        return $this->render('pages/accueil.html.twig', [
            'formations' => $formations,
        ]);
    }

    /**
     * Affiche la page des conditions générales d'utilisation (CGU)
     *
     * @return Response
     */
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render('pages/cgu.html.twig');
    }
}
