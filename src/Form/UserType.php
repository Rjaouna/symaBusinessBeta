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

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeClient', TextType::class, [
                'label' => 'Code Client',
                'attr' => [
                    'readonly' => true,
                ],
            ])
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
