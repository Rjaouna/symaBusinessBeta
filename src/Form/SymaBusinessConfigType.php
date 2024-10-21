<?php

namespace App\Form;

use App\Entity\SymaBusinessConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SymaBusinessConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomDuResponsable', TextType::class, [
                'label' => false, // Retire le label
                'attr' => ['placeholder' => 'Nom du responsable'], // Ajoute le placeholder
            ])
            ->add('email', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Adresse e-mail'],
            ])
            ->add('numeroDeTelephone', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Numéro de téléphone'],
            ])
            ->add('numeroDeFixe', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Numéro de fixe'],
            ])
            ->add('raisonSociale', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Raison sociale'],
            ])
            ->add('numeroDeRegistre', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Numéro de registre'],
            ])
            ->add('formeJuridique', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Forme juridique'],
            ])
            ->add('codeApeNaf', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Code APE/NAF'],
            ])
            ->add('capitalSocial', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Capital social'],
            ])
            ->add('numeroDeTvaIntracommunautaire', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Numéro de TVA intracommunautaire'],
            ])
            ->add('numeroSiret', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Numéro SIRET'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SymaBusinessConfig::class,
        ]);
    }
}
