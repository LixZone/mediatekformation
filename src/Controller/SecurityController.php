<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur de sécurité
 *
 * Gère l'authentification des utilisateurs (connexion et déconnexion)
 * ainsi que l'accès au back office.
 */
class SecurityController extends AbstractController
{
    /**
     * Affiche la page de connexion
     *
     * Récupère les erreurs de connexion et le dernier identifiant saisi
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier identifiant saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
    
    /**
     * Page d'entrée du back office
     *
     * Redirige vers la liste des formations
     *
     * @return Response
     */
    #[Route('/admin', name: 'admin.home')]
    public function index(): Response
    {
        return $this->redirectToRoute('admin.formations');
    }

    /**
     * Déconnexion de l'utilisateur
     *
     * Cette méthode est interceptée automatiquement par Symfony
     *
     * @throws \LogicException
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
