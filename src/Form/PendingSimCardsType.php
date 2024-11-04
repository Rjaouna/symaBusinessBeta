<?php

namespace App\Form;

use App\Entity\SimType;
use App\Entity\PendingSimCards;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PendingSimCardsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serialNumber')
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
        $resolver->setDefaults([
            'data_class' => PendingSimCards::class,
        ]);
    }
}
