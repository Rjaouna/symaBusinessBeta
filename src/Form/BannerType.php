<?php

namespace App\Form;

use App\Entity\Banner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('image', FileType::class, [
                'label' => 'Image (fichier PNG ou JPEG)', // Personnaliser le label
                'mapped' => false, // Ce champ n'est pas directement lié à l'entité Banner
                'required' => false, // Rendre l'upload optionnel
                'attr' => [
                    'accept' => 'image/png, image/jpeg', // Restreindre les types de fichiers acceptés
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,
        ]);
    }
}
