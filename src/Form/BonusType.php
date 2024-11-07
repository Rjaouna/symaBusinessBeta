<?php

namespace App\Form;

use App\Entity\Bonus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

class BonusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('valeur', IntegerType::class, [
            'label' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'La valeur ne peut pas être vide.',
                ]),
                new Range([
                    'min' => 10,
                    'max' => 500,
                    'notInRangeMessage' => 'La valeur doit être comprise entre {{ min }} et {{ max }}.',
                ]),
            ],
            'attr' => [
                'min' => 10, // Pour le contrôle dans l'interface utilisateur (HTML5)
                'max' => 500,
                'step' => 1, // Optionnel : uniquement des entiers
            ],
            'attr' => [
                'placeholder' => 'Entrez la valeur de bonus',
            ],
        ])
            ->add('motif', TextType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le motif est obligatoire.',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le motif ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Entrez le motif',
                ],
            ])
            ->add('user', EntityType::class, [
            'label' => false,
                'class' => User::class,
            'choice_label' => 'nomResponsable', // Cela affichera l'email dans le choix des utilisateurs
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
