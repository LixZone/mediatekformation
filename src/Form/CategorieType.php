<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Formulaire CategorieType
 * 
 * Permet de créer et modifier une entité Categorie via le système de formulaires Symfony.
 */
class CategorieType extends AbstractType
{
    /**
     * Construction du formulaire
     * 
     * Définit les champs affichés et leurs options de configuration
     *
     * @param FormBuilderInterface $builder Constructeur du formulaire
     * @param array $options Options de configuration du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie : '
            ]);
    }
    
    /**
     * Configuration des options du formulaire
     * 
     * Définit la classe de données liée au formulaire
     *
     * @param OptionsResolver $resolver Résolveur des options
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
