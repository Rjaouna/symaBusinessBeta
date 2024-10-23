<?php

namespace App\Controller\InterfacesUsers;

use App\Entity\Bonus;
use App\Form\BonusType;
use App\Repository\UserRepository;
use App\Repository\BonusRepository;
use App\Repository\QuotaRepository;
use App\Repository\SimTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/userbonus')]
final class UserBonusController extends AbstractController
{
	#[Route('/app/user/Bonus', name: 'app_user_Bonus_Controller')]
	public function gestion(SimTypeRepository $simTypeRepository, QuotaRepository $quotaRepository): Response
	{
		// Récupérer tous les types de cartes SIM depuis la base de données
		$typesCartesSim = $simTypeRepository->findAll();

		// Récupérer l'utilisateur connecté
		$user = $this->getUser();

		// Récupérer le total des bonus de l'utilisateur
		$totalBonus = $user->getTotalBonus();

		// Récupérer le quota de l'utilisateur
		$quota = $user->getQuotas();
		if ($quota) {
			// Si l'utilisateur a un quota, récupérer ses informations
			$idQuota = $quota->getId(); // Récupérer l'ID du quota
			$quotasUser = $quotaRepository->find($idQuota); // Récupérer l'objet Quota à partir de l'ID

			// Accéder aux propriétés du quota via des getters
			$quotaSim5Quota = $quotasUser->getSim5Quota();
			$quotaSim10Quota = $quotasUser->getSim10Quota();
			$quotaSim15Quota = $quotasUser->getSim15Quota(); // Correction de l'appel au getter
			$quotaSim20Quota = $quotasUser->getSim20Quota();
		} else {
			// Gérer le cas où l'utilisateur n'a pas de quota
			$quotasUser = null;
			$quotaNom = null;
			$quotaSim5Quota = null;
			$quotaSim10Quota = null;
			$quotaSim15Quota = null;
			$quotaSim20Quota = null;
		}

		// Créer un tableau pour stocker les usages des cartes SIM
		$usages = [];

		// Remplir le tableau avec les usages en fonction des types de cartes SIM
		foreach ($typesCartesSim as $typeCarte) {
			switch ($typeCarte->getCode()) {
				case 'cartesim05':
					$usages[$typeCarte->getNom()] = $user->getSim5Usage();
					break;

				case 'cartesim10':
					$usages[$typeCarte->getNom()] = $user->getSim10Usage();
					break;

				case 'cartesim15':
					$usages[$typeCarte->getNom()] = $user->getSim15Usage();
					break;

				case 'cartesim20':
					$usages[$typeCarte->getNom()] = $user->getSim20Usage();
					break;

				default:
					// Optionnel : gérer les types non reconnus
					$usages[$typeCarte->getNom()] = null; // ou une valeur par défaut
					break;
			}
		}

		// Rendre la vue de gestion des bonus avec les données nécessaires
		return $this->render('interfaces_users/bonus/bonus_manager.html.twig', [
			'user' => $user,
			'typesCartesSim' => $typesCartesSim,
			'totalBonus' => $totalBonus,
			'usages' => $usages,
			'quotaSim5Quota' => $quotaSim5Quota,
			'quotaSim10Quota' => $quotaSim10Quota,
			'quotaSim15Quota' => $quotaSim15Quota,
			'quotaSim20Quota' => $quotaSim20Quota,
		]);
	}
}
