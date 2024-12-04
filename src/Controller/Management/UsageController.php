<?php

namespace App\Controller\Management;

use App\Repository\UsageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsageController extends AbstractController
{
	#[Route('/admin/usages/reset-consommation', name: 'reset_consommation', methods: ['GET'])]
	public function resetConsommation(UsageRepository $usageRepository, EntityManagerInterface $entityManager): Response
	{
		// Vérifier que l'utilisateur a le rôle d'administrateur
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		// Récupérer toutes les entrées Usage
		$usages = $usageRepository->findAll();

		// Mettre à jour la consommation à 0 pour chaque Usage
		foreach ($usages as $usage) {
			$usage->setConsomation(0);
		}

		// Sauvegarder les modifications en base de données
		$entityManager->flush();

		// Ajouter un message flash pour informer l'utilisateur
		$this->addFlash('success', 'La consommation de tous les utilisateurs a été réinitialisée à 0.');

		// Rediriger vers la liste des usages
		return $this->redirectToRoute('app_syma_business');
	}
}
