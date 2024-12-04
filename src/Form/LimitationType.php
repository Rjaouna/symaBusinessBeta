<?php

namespace App\Form;

use App\Entity\Limitation;
use App\Entity\SimType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LimitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeCarte', EntityType::class, [
                'class' => SimType::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un type de carte',
                'attr' => [
                    'class' => 'form-control',
                    'data-mdb-input-init' => true, // Si vous utilisez MDB (Material Design Bootstrap)
                ],
                'label' => false, // Vous gérez le label avec un `form-label` personnalisé
            ])
            ->add('limite', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la limite',
                    'data-mdb-input-init' => true, // Initialisation pour MDB
                ],
                'label' => false, // Vous gérez le label avec un `form-label` personnalisé
            ])
            ->add('prix', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le prix',
                    'data-mdb-input-init' => true, // Initialisation pour MDB
                ],
                'label' => false, // Vous gérez le label avec un `form-label` personnalisé
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Limitation::class,
        ]);
    }
}
