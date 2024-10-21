<?php

namespace App\Form;

use App\Entity\Bonus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BonusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('valeur')
            ->add('motif')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email', // Cela affichera l'email dans le choix des utilisateurs
                'placeholder' => 'Sélectionnez un utilisateur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bonus::class,
        ]);
    }
}
