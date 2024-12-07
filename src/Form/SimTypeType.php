<?php

namespace App\Form;

use App\Entity\SimType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Nom de la carte',
            ],
        ])
            ->add('code', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Code de la carte (le nom sans espaces)',
                ],
            ])
            ->add('prix', TextType::class, [
                'label' => false,
                'attr' => [],
            ])
            ->add('quotaSimOffertes', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => '* Nombre maximum de chapelets offerts',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SimType::class,
        ]);
    }
}
