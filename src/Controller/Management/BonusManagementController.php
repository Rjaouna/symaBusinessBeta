<?php

namespace App\Controller\Management;

use App\Entity\Bonus;
use App\Form\BonusType;
use App\Repository\UserRepository;
use App\Repository\BonusRepository;
use App\Repository\QuotaRepository;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/bonus/management')]
final class BonusManagementController extends AbstractController
{
	#[Route('/updater', name: 'app_bonus_management')]
	public function updateSimUsage(Request $request, EntityManagerInterface $entityManager, SimTypeRepository $simTypeRepository, BonusRepository $bonusRepository): Response
	{
		// Récupération de l'utilisateur connecté
		/** @var User $user */
		$user = $this->getUser();

		if (!$user) {
			return $this->redirectToRoute('login'); // Redirection vers la page de connexion
		}

		// Récupération des paramètres de la requête
		$remainingBonus = $request->query->get('remainingBonus');
		$orderItemsJson = $request->query->get('orderItems');

		// Décoder le JSON pour obtenir les articles de commande
		$orderItems = json_decode($orderItemsJson, true);

		// Vérification des données envoyées
		if (!is_array($orderItems)) {
			return $this->redirectToRoute('error_page', ['message' => 'Données invalides, orderItems est requis']);
		}

		// Traitement des articles commandés
		foreach ($orderItems as $item) {
			if (!isset($item['typeCarte']) || !isset($item['quantity']) || $item['quantity'] <= 0) {
				return $this->redirectToRoute('error_page', ['message' => 'Type de carte ou quantité manquante']);
			}

			$typeCarte = $item['typeCarte'];
			$quantity = (int)$item['quantity'];

			// Récupération du type de SIM à partir de la base de données
			$simTypeEntity = $simTypeRepository->findOneBy(['nom' => $typeCarte]);
			$code = strtolower($simTypeEntity->getCode());

			if (!$code) {
				return $this->redirectToRoute('error_page', ['message' => 'Type de SIM invalide : ' . $typeCarte]);
			}

			$currentSimUsage = null;

			switch ($code) {
				case 'cartesim05':
					$currentSimUsage = $user->getSim5Usage();
					break;


				case 'cartesim10':
					$currentSimUsage = $user->getSim10Usage();

					break;

				case 'cartesim15':
					$currentSimUsage = $user->getSim15Usage();
					break;

				case 'cartesim20':
					$currentSimUsage = $user->getSim20Usage();
					break;

				default:
					return $this->redirectToRoute('app_user_Bonus_Controller', ['message' => 'Type de SIM invalide : ' . $typeCarte]);
			}

			// Vérification de la quantité demandée par rapport à la consommation disponible
			if ($currentSimUsage < $quantity) {
				$this->addFlash('danger', 'Quantité demandée supérieure à la consommation disponible pour ' . $typeCarte);
				return $this->redirectToRoute('app_user_Bonus_Controller');
			}

			// Mise à jour de la consommation
			switch ($simTypeEntity->getCode()) {
				case 'cartesim05':
					$user->setSim5Usage($currentSimUsage - $quantity);
					break;

				case 'cartesim10':
					$user->setSim10Usage($currentSimUsage - $quantity);
					break;

				case 'cartesim15':
					$user->setSim15Usage($currentSimUsage - $quantity);
					break;

				case 'cartesim20':
					$user->setSim20Usage($currentSimUsage - $quantity);
					break;
			}
		}

		$user->setTotalBonus($remainingBonus);

		// Sauvegarde dans la base de données
		$entityManager->persist($user);
		$entityManager->flush();

		// Redirection vers une page de succès
		return $this->redirectToRoute('app_user_Bonus_Controller', [
			'message' => 'Consommation mise à jour avec succès',
			'remainingBonus' => $remainingBonus
		]);
	}
}
