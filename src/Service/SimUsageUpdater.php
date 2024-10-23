<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SimUsageUpdater
{
	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * Met à jour l'usage du type de SIM pour un utilisateur donné.
	 *
	 * @param User   $user    L'utilisateur à mettre à jour
	 * @param string $simType Le type de SIM (ex: 'sim5', 'sim10', 'sim15', etc.)
	 * @param int    $quantity La quantité à ajouter à l'usage actuel
	 */
	public function updateSimUsage(User $user, string $simType, int $quantity): void
	{
		// Sélectionne le type de SIM à mettre à jour
		switch ($simType) {
			case 'cartesim05':
				$user->setSim5Usage($user->getSim5Usage() + $quantity);
				break;
			case 'cartesim10':
				$user->setSim10Usage($user->getSim10Usage() + $quantity);
				break;
			case 'cartesim15':
				$user->setSim15Usage($user->getSim15Usage() + $quantity);
				break;
			case 'cartesim20':
				$user->setSim20Usage($user->getSim20Usage() + $quantity);
				break;
			default:
				throw new \InvalidArgumentException('Type de SIM inconnu: ' . $simType);
		}

		// Sauvegarder les changements dans la base de données
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}
}
