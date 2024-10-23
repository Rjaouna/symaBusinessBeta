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
			->add('telephoneFixe')
			->add('telephoneMobile')
			->add('nomSociete')
			->add('formeJuridique')
			->add('numeroRegistreCommerce')
			->add('numeroSiret')
			->add('numeroRCS')
		->add('codeAPE')
			->add('kbis')
			->add('adresse')
			->add('pays')
			->add('codePostal')
			->add('ville')
			->add('iban')
			->add('bic')

		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}
