<?php

namespace App\Form;

use App\Entity\SimType;
use App\Entity\PendingSimCards;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;


class PendingSimCardsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serialNumber', TextType::class, [
                'attr' => array_merge(
                    $options['is_new'] ? [] : ['readonly' => 'readonly'],
                    ['maxlength' => 19] // Limit the input to 19 characters
                ),
                'constraints' => [
                    new Assert\NotBlank(), // Assurez-vous que le champ n'est pas vide
                    new Assert\Length([
                        'min' => 19, // Limiter à 19 caractères minimum
                        'max' => 19, // Limiter à 19 caractères maximum
                        'minMessage' => 'Le numéro de série doit contenir exactement {{ limit }} chiffres.',
                        'maxMessage' => 'Le numéro de série doit contenir exactement {{ limit }} chiffres.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^\d{19}$/', // Vérifie que le champ contient exactement 19 chiffres
                        'message' => 'Le numéro de série doit contenir uniquement 19 chiffres.',
                    ]),
                ],
            ])
            ->add('type', EntityType::class, [
                'class' => SimType::class,
                'choice_label' => function (SimType $simType) {
                    return $simType->getNom(); // Display the name of the SimType
                },
                'choice_value' => function (SimType $simType = null) {
                    return $simType ? $simType->getCode() : ''; // Use the code as the value
                },
                'placeholder' => 'Select a SIM type', // Placeholder text
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PendingSimCards::class,
            'is_new' => true, 
        ]);
    }
}
