<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientSelectAdvincedType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('client', EntityType::class, [
				'class' => User::class,
				'choice_label' => 'nomResponsable', // Ce qui sera affiché dans la liste déroulante
				'placeholder' => 'Sélectionnez un client',
				'label' => false,
			'required' => true,
				'query_builder' => function (UserRepository $userRepository) {
					return $userRepository->createQueryBuilder('u')
						->where('u.sim5Usage >= 0 AND u.sim10Usage >= 0 AND u.sim15Usage >= 0 AND u.sim20Usage >= 0');
				}

			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			// Pas besoin de mapper à une entité spécifique ici
		]);
	}
}
