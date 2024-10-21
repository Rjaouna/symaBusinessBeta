<?php

namespace App\Form;

use App\Entity\Bonus;
use App\Entity\Quota;
use App\Entity\Usage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('nomResponsable')
            ->add('telephoneFixe')
            ->add('telephoneMobile')
            ->add('nomSociete')
            ->add('formeJuridique')
            ->add('numeroRegistreCommerce')
            ->add('numeroSiret')
            ->add('numeroRCS')
            ->add('codeAPE')
            ->add('facade')
            ->add('kbis')
            ->add('adresse')
            ->add('pays')
            ->add('codePostal')
            ->add('ville')
            ->add('codeClient')
            ->add('iban')
            ->add('bic')
            ->add('quotas', EntityType::class, [
                'class' => Quota::class,
                'choice_label' => 'nom',
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
