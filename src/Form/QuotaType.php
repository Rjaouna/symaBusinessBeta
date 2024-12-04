<?php

namespace App\Form;

use App\Entity\Quota;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class QuotaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer les codes déjà utilisés
        $builder
        ->add('nom', null, [
            'label' => false, // Retire le label
            'attr' => [
                'placeholder' => 'Entrez le nom'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le nom est obligatoire.',
                ]),
                new Regex([
                    'pattern' => '/^[a-zA-Z\s\-]+$/u',
                    'message' => 'Le nom ne peut contenir que des lettres, des espaces ou des traits d’union.',
                ]),
            ],
        ])
        ->add(
            'code',
            null,
            [
                'label' => false, // Retire le label
                'attr' => [
                    'placeholder' => 'Entrez le code'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le code est obligatoire.',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\-]+$/u',
                        'message' => 'Le nom ne peut contenir que des lettres, des espaces ou des traits d’union.',
                    ]),
                ],
            ]
        )
        ->add('sim5Quota', null, [
            'label' => false,
            'attr' => ['placeholder' => 'Entrez le quota pour Sim 05'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le quota pour Sim 05 est obligatoire.',
                ]),
                new Type([
                    'type' => 'numeric',
                    'message' => 'Veuillez entrer un nombre valide.',
                ]),
                new GreaterThan([
                    'value' => 0,
                    'message' => 'Le quota pour Sim 05 doit être supérieur à 0.',
                ]),
            ],
        ])
        ->add('sim10Quota', null, [
            'label' => false,
            'attr' => ['placeholder' => 'Entrez le quota pour Sim 10'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le quota pour Sim 10 est obligatoire.',
                ]),
                new Type([
                    'type' => 'numeric',
                    'message' => 'Veuillez entrer un nombre valide.',
                ]),
                new GreaterThan([
                    'value' => 0,
                    'message' => 'Le quota pour Sim 10 doit être supérieur à 0.',
                ]),
            ],
        ])
        ->add('sim15Quota', null, [
            'label' => false,
            'attr' => ['placeholder' => 'Entrez le quota pour Sim 15'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le quota pour Sim 15 est obligatoire.',
                ]),
                new Type([
                    'type' => 'numeric',
                    'message' => 'Veuillez entrer un nombre valide.',
                ]),
                new GreaterThan([
                    'value' => 0,
                    'message' => 'Le quota pour Sim 15 doit être supérieur à 0.',
                ]),
            ],
        ])
        ->add('sim20Quota', null, [
            'label' => false,
            'attr' => ['placeholder' => 'Entrez le quota pour Sim 20'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Le quota pour Sim 20 est obligatoire.',
                ]),
                new Type([
                    'type' => 'numeric',
                    'message' => 'Veuillez entrer un nombre valide.',
                ]),
                new GreaterThan([
                    'value' => 0,
                    'message' => 'Le quota pour Sim 20 doit être supérieur à 0.',
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quota::class,
        ]);
    }
}
