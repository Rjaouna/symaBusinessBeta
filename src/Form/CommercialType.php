<?php
// src/Form/CommercialType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class CommercialType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			// Champ Nom du Responsable
			->add('nomResponsable', TextType::class, [
				'label' => 'Nom du Responsable',
				'attr' => [
					'placeholder' => 'Nom du Responsable',
				],
			])
			// Champ Adresse E-mail
			->add('email', EmailType::class, [
				'label' => 'Adresse E-mail',
				'attr' => [
					'placeholder' => 'Adresse e-mail du Commercial',
				],
			])
			// Champ Numéro de Téléphone
			->add('telephoneMobile', TextType::class, [
				'label' => 'Numéro de Téléphone Mobile',
				'attr' => [
					'placeholder' => 'Numéro de Téléphone Mobile',
				],
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			// Associe ce formulaire à l'entité User
			'data_class' => User::class,
		]);
	}
}