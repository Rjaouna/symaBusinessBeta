<?php
// src/Form/SerialNumberType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeValidationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('serial_Number', TextType::class, [
			'attr' => [
				'placeholder' => 'Scannez un code !',
				'maxlength' => 14, // Limite le nombre de caractères à 14
			],
			'label' => false, // Retire le label
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			// Configure your form options here
		]);
	}
}