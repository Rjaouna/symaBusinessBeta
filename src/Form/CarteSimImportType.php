<?php

namespace App\Form;

use App\Entity\SimType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CarteSimImportType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('csvFile', FileType::class, [
				'label' => 'Fichier CSV',
				'mapped' => false,
				'required' => true,
			])
			->add('simType', EntityType::class, [
				'class' => SimType::class,
				'choice_label' => 'nom', // Affiche le nom dans le select
				'label' => 'Type de carte SIM',
				'placeholder' => 'Choisir un type',
				'required' => true,
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([]);
	}
}
