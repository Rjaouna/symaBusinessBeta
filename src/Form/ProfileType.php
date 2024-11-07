<?php
// src/Form/ProfileType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class, [
				'attr' => ['readonly' => true], // Rendre le champ en lecture seule
			])
			->add('nomResponsable')
			->add('telephoneMobile')
		->add('nomSociete')
		->add('numeroSiret')
			->add('adresse')
		->add('pays')
		->add('ville')

		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}
