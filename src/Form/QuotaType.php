<?php

namespace App\Form;

use App\Entity\Quota;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuotaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', null, [
            'label' => false, // Retire le label
            'attr' => ['placeholder' => 'Entrez le nom'] // Ajoute un placeholder
        ])
        ->add('sim5Quota', null, [
            'label' => false,
            'attr' => ['placeholder' => 'Entrez le quota pour Sim 05']
        ])
        ->add('sim10Quota', null, [
            'label' => false,
            'attr' => ['placeholder' => 'Entrez le quota pour Sim 10']
        ])
        ->add('sim15Quota', null, [
            'label' => false,
            'attr' => ['placeholder' => 'Entrez le quota pour Sim 15']
        ])
        ->add('sim20Quota', null, [
            'label' => false,
            'attr' => ['placeholder' => 'Entrez le quota pour Sim 20']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quota::class,
        ]);
    }
}
