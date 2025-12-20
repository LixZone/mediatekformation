<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire FormationType
 * 
 * Permet la création et la modification d'une entité Formation.
 * Gère les relations avec Playlist et Categorie.
 */
class FormationType extends AbstractType
{
    /**
     * Construction du formulaire
     * 
     * Définit les champs nécessaires à la gestion d'une formation :
     * - informations textuelles
     * - relations avec playlist et catégories
     * - date de publication
     *
     * @param FormBuilderInterface $builder Constructeur du formulaire
     * @param array $options Options de configuration du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre * :'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description :'
            ])   
            ->add('videoId', TextType::class, [
                'label' => 'ID Vidéo YouTube * :',
                'required' => false,
            ])  
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'label' => 'Playlist * :'
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label' => 'Catégories :'
            ])
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date * :',
                'attr' => [
                    'max' => (new \DateTime())->format('Y-m-d')
                ]
            ]);
    }

    /**
     * Configuration des options du formulaire
     * 
     * Définit la classe de données liée (Formation)
     *
     * @param OptionsResolver $resolver Résolveur de configuration
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
