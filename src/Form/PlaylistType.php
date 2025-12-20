<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Formulaire PlaylistType
 * 
 * Permet la création et la modification d'une entité Playlist via Symfony Forms.
 */
class PlaylistType extends AbstractType
{
    /**
     * Construction du formulaire
     * 
     * Définit les champs nécessaires à la gestion d'une playlist :
     * - nom
     * - description
     *
     * @param FormBuilderInterface $builder Constructeur du formulaire
     * @param array $options Options de configuration du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre * :'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description :'
            ]);
    }
    
    /**
     * Configuration des options du formulaire
     * 
     * Définit la classe de données associée (Playlist)
     *
     * @param OptionsResolver $resolver Résolveur de configuration
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
