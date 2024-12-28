<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Zone;
use App\Entity\Bonus;
use App\Entity\Quota;
use App\Entity\Usage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeClient', TextType::class, [
                'label' => 'Code Client',
            'attr' => [
                'class' => 'form-control-lg',
                'placeholder' => 'Ex : C41027544',
                'readonly' => true,

            ],
            'required' => false,
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
            'required' => false,
            ])

            ->add('numeroSiret', TextType::class, [
                'label' => 'Numero siret',
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            'required' => false,
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse postale',
                'attr' => [
                    'class' => 'form-control-lg',
                ],
            'required' => false,
            ])
            ->add('codeZone', EntityType::class, [
                'class' => Zone::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une zone',
                'label' => 'Zone Géographique',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('pays', TextType::class, [
                'attr' => [
                    'class' => 'form-control-lg',
                    'readonly' => true,
                    'value' => 'France',
                ],
            ])

            
            ->add('quotas', EntityType::class, [
            'label' => 'Quota',
                'class' => Quota::class,
                'choice_label' => 'nom',
            'attr' => [
                'class' => 'form-control-lg',
            ],
            'required' => false,
            ])
            // ->add('roles', ChoiceType::class, [
            //     'label' => 'Rôle',
            //     'choices'  => [
            //         'Administrateur' => 'ROLE_ADMIN',
            //         'Utilisateur' => 'ROLE_USER',
            //         'Super Administrateur' => 'ROLE_SUPER_ADMIN',
            //         'Commercial' => 'ROLE_COMMERCIAL',
            //     ],
            //     'expanded' => true, // Affiche des cases à cocher
            //     'multiple' => true, // Permet de sélectionner plusieurs rôles
            //     'required' => true,

            // ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
