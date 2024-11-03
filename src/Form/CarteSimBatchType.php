<?php
// src/Form/CarteSimBatchType.php
namespace App\Form;

use App\Entity\SimType; // Assurez-vous d'importer l'entité SimType
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarteSimBatchType extends AbstractType
{
	private $simTypeRepository;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->simTypeRepository = $entityManager->getRepository(SimType::class);
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('firstSerialNumber', TextType::class, [
				'label' => false,
				'attr' => [
					'placeholder' => 'Entrez le premier numéro de série (14 chiffres)',
					'maxlength' => 14,
				'pattern' => '[0-9]{19}',
					'title' => 'Le numéro de série doit contenir exactement 14 chiffres',
				],
			])
			->add('lastSerialNumber', TextType::class, [
				'label' => false,
				'attr' => [
					'placeholder' => 'Entrez le dernier numéro de série (14 chiffres)',
				'maxlength' => 19,
				'pattern' => '[0-9]{19}',
					'title' => 'Le numéro de série doit contenir exactement 14 chiffres',
				],
			])
			->add('typeCarteSim', ChoiceType::class, [
				'label' => false,
				'choices' => $this->getSimTypeChoices(),
				'placeholder' => 'Sélectionnez le type de carte SIM',
				'attr' => [
					'onchange' => 'compareSerialNumbers()'
				],
			]);
	}

	private function getSimTypeChoices()
	{
		$simTypes = $this->simTypeRepository->findAll();
		$choices = [];
		foreach ($simTypes as $simType) {
			$choices[$simType->getNom()] = $simType->getNom(); // Ou autre champ unique
		}
		return $choices;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([]);
	}
}
