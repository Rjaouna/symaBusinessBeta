<?php
// src/Form/CarteSimType.php

namespace App\Form;

use App\Entity\User;
use App\Entity\SimType;
use App\Entity\CarteSim;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CarteSimType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serialNumber', TextType::class, [
                'attr' => [
                    'placeholder' => 'Entrez le numéro de série ici', // Votre texte de placeholder
                ],
            ])
            ->add('reserved', CheckboxType::class, [
                'label' => 'Je souhaite attribuer cette carte à un client', // Votre texte de label
                'required' => false, // Permet de ne pas sélectionner de client
                'attr' => [
                    'id' => 'reserved_checkbox', // Ajoutez un ID pour le JavaScript
                ],
            ])
            ->add('purchasedBy', EntityType::class, [
                'class' => User::class,
            'choice_label' => 'nomResponsable',
            'placeholder' => 'Choisissez un client', // Texte à afficher comme premier choix
            'required' => false, // Permet de ne pas sélectionner de client
            'label' => false, // Supprime le label
            'attr' => [
                'id' => 'purchased_by_select', // Ajoutez un ID pour le JavaScript
                'style' => 'display:none;', // Masquez le champ par défaut
            ],
            ])
            ->add('type', EntityType::class, [
                'class' => SimType::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarteSim::class,
        ]);
    }
}
