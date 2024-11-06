<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Bonus;
use App\Entity\Quota;
use App\Entity\Usage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeClient', TextType::class, [
                'label' => 'Code Client',
                'attr' => [
                    'readonly' => true,
                'class' => 'form-control-lg',
                'placeholder' => 'Ex : C41027544',
            ],
            'constraints' => [
                new Regex([
                    'pattern' => '/^[A-Za-z][0-9]{8}$/',
                    'message' => 'Le code client doit commencer par une lettre suivie de 8 chiffres. Ex : C41027544',
                ]),
                ],
            ])
            ->add('nomResponsable', TextType::class, [
                'label' => 'Nom resposable',
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Adresse E-mail',
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])

            ->add('telephoneMobile', TextType::class, [
                'label' => 'Téléphone portable',
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])
            ->add('nomSociete', TextType::class, [
                'label' => 'Raison sociale',
                'attr' => [
                    'class' => 'form-control-lg', // Remplacez par la classe CSS souhaitée
                ],
            ])

            ->add('numeroSiret', TextType::class, [
                'label' => 'Numero siret',
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse postale',
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])
            ->add('pays', TextType::class, [
                'attr' => [
                    'class' => 'form-control-lg',
                    'readonly' => true,
                    'value' => 'France',
                ],
            ])

            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            ])
            ->add('quotas', EntityType::class, [
            'label' => 'Quota',
                'class' => Quota::class,
                'choice_label' => 'nom',
            'attr' => [
                'class' => 'form-control-lg',
            ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
