<?php
// src/Form/SerialNumberFormType.php
namespace App\Form;

use App\Entity\SimType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerialNumberFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('prefix', TextType::class, [
				'attr' => ['placeholder' => 'Entrez le préfixe (90045)'],
				'label' => false
			])
			->add('start', IntegerType::class, [
				'attr' => ['placeholder' => 'Entrez le début (90)'],
				'label' => false
			])
			->add('end', IntegerType::class, [
				'attr' => ['placeholder' => 'Entrez la fin (99)'],
				'label' => false
			])
			->add('suffix', TextType::class, [
				'attr' => ['placeholder' => 'Entrez le suffixe (5847589)'],
				'label' => false
			])
			->add('simType', EntityType::class, [
				'class' => SimType::class,
				'choice_label' => 'nom', // Supposant que 'nom' est le nom du type
				'placeholder' => 'Choisissez le type des cartes', // Texte à afficher comme premier choix
				'label' => false, // Pour ne pas afficher de label
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([]);
	}
}
