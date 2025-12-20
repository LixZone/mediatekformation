<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur permettant de gérer les catégories dans le back office
 *
 * Permet d'afficher, ajouter et supprimer des catégories.
 */
class CategoriesController extends AbstractController
{
    /**
     * Affiche la liste des catégories et gère l'ajout d'une nouvelle catégorie
     *
     * Vérifie également qu'une catégorie avec le même nom n'existe pas déjà
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérifie si une catégorie avec le même nom existe déjà
            $existing = $em->getRepository(Categorie::class)
                ->findOneBy(['name' => $categorie->getName()]);

            if (!$existing) {
                $em->persist($categorie);
                $em->flush();
            }

            return $this->redirectToRoute('admin.categories');
        }

        // Récupère toutes les catégories
        $categories = $em->getRepository(Categorie::class)->findAll();

        return $this->render('admin/categories.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    /**
     * Supprime une catégorie
     *
     * La suppression est interdite si la catégorie est liée à des formations
     *
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     */
    #[Route('/admin/categories/remove/{id}', name: 'admin.categories.remove', methods: ['POST'])]
    public function remove(EntityManagerInterface $em, $id): Response
    {
        // Recherche la catégorie par son identifiant
        $categorie = $em->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException("Catégorie non trouvée");
        }
        
        // Vérifie si la catégorie est utilisée par des formations
        if (count($categorie->getFormations()) > 0) {
            $this->addFlash('error', 'Impossible de supprimer : catégorie utilisée');
            return $this->redirectToRoute('admin.categories');
        }
        
        // Suppression de la catégorie
        $em->remove($categorie);
        $em->flush();

        return $this->redirectToRoute('admin.categories');
    }
}
