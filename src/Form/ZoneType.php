<?php

namespace App\Form;

use App\Entity\Zone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZoneType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder

			->add('nom', TextType::class, [
				'label' => 'Nom de la Zone',
				'attr' => ['placeholder' => 'Entrez le nom de la zone'],
			])
			->add('description', TextareaType::class, [
				'label' => 'Description de la zone',
				'attr' => ['placeholder' => 'Description de la zone'],
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Zone::class,
		]);
	}
}
