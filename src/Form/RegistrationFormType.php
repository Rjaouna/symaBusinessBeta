<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomResponsable', TextType::class, [
                'label' => 'Nom du Responsable',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\w+\s\w+$/',
                        'message' => 'Le nom du responsable doit contenir un prénom et un nom séparés par un espace.',
                    ]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Prénom Nom']
            ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'adresse est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 30,
                        'minMessage' => 'L\'adresse doit contenir au moins {{ limit }} caractères.',
                        'max' => 100,
                        'maxMessage' => 'L\'adresse ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Adresse postale',
                ],
                'label' => 'Adresse',
            ])
            ->add('telephoneMobile', TextType::class, [
                'label' => 'Téléphone Mobile',
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'max' => 15,
                        'minMessage' => 'Le numéro de téléphone mobile doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le numéro de téléphone mobile ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/^\+?[0-9]{10,15}$/',
                        'message' => 'Veuillez entrer un numéro de téléphone valide (ex : +1234567890).'
                    ])
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Numéro de téléphone mobile']
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
            'label' => 'J\'accepte les termes et conditions',
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse E-mail',
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas valide.', // Message d'erreur personnalisé
                    ]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Adresse E-mail']
            ])
            ->add('nomSociete', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom de la société ne peut pas être vide.',
                    ]),
                    new Assert\Length([
                        'max' => 20,
                        'maxMessage' => 'Le nom de la société ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z0-9\s\-\.]+$/',
                        'message' => 'Le nom de la société ne peut contenir que des lettres, des chiffres, des espaces, des tirets et des points.',
                    ]),
                ],
            ])
            ->add('numeroSiret', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le numéro SIRET ne peut pas être vide.']),
                    new Assert\Length([
                        'min' => 14,
                        'max' => 14,
                        'exactMessage' => 'Le numéro SIRET doit contenir exactement 14 chiffres.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^\d{14}$/',
                        'message' => 'Le numéro SIRET doit être composé uniquement de chiffres.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control-lg ps-5 w-100',
                    'placeholder' => 'Numéro SIRET'
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
