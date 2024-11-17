<?php
// src/Form/PaiementFactureType.php

namespace App\Form;

use App\Entity\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementFactureType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('modePaiement', ChoiceType::class, [
				'choices' => [
					'Carte bancaire' => 'carte_bancaire',
					'Virement bancaire' => 'virement_bancaire',
					'Chèque' => 'cheque',
					'Espèces' => 'especes',
				],
				'label' => 'Mode de paiement',
				'placeholder' => 'Choisissez un mode de paiement',
			])
			->add('submit', SubmitType::class, [
				'label' => 'Valider le paiement',
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Facture::class,
		]);
	}
}
