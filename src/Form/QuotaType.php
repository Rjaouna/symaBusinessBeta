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
            ->add('nom')
            ->add('sim5Quota')
            ->add('sim10Quota')
            ->add('sim15Quota')
            ->add('sim20Quota')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quota::class,
        ]);
    }
}
