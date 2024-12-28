<?php

namespace App\Form;

use App\Entity\Tournee;
use App\Entity\Commercial;
use App\Entity\Zone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourneeType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('commercial', EntityType::class, [
				'class' => Commercial::class,
				'choice_label' => 'nom',
				'label' => 'Commercial',
			])
			->add('zone', EntityType::class, [
				'class' => Zone::class,
				'choice_label' => 'nom',
				'label' => 'Zone Géographique',
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder('z')
						->where('z.statut IS NULL');
				},

			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Tournee::class,
		]);
	}
}
