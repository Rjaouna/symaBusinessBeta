<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientSelectType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('client', EntityType::class, [
				'class' => User::class,
				'choice_label' => 'nomResponsable', // Ce qui sera affiché dans la liste déroulante
				'placeholder' => 'Sélectionnez un client',
				'label' => false,
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			// Pas besoin de mapper à une entité spécifique ici
		]);
	}
}
