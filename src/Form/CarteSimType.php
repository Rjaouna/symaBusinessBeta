<?php

namespace App\Form;

use App\Entity\CarteSim;
use App\Entity\SimType;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarteSimType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serialNumber')
            ->add('reserved')
            ->add('purchasedBy', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'codeClient',
            ])
            ->add('type', EntityType::class, [
                'class' => SimType::class,
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarteSim::class,
        ]);
    }
}
