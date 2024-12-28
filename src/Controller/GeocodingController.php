<?php
// src/Controller/GeocodingController.php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\GeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/geocoding')]
class GeocodingController extends AbstractController
{
	/**
	 * @Route("/update-coordinates", name="geocoding_update_coordinates", methods={"GET"})
	 */
	#[Route('/update-coordinates', name: 'geocoding_update_coordinates', methods: ['GET'])]
	public function updateCoordinates(
		UserRepository $userRepository,
		GeocodingService $geocodingService,
		EntityManagerInterface $entityManager
	): Response {
		// Récupérer tous les utilisateurs sans coordonnées
		$users = $userRepository->findBy(['latitude' => null, 'longitude' => null]);

		if (empty($users)) {
			$this->addFlash('info', 'Aucun utilisateur sans coordonnées trouvé.');
			return $this->redirectToRoute('app_syma_business'); // Assurez-vous que cette route existe
		}
		$missingAddress = false;

		foreach ($users as $user) {
			$address = $user->getAdresse(); // Assurez-vous que cette méthode existe et retourne une chaîne d'adresse complète

			if (empty($address)) {
				$missingAddress = true;
				continue;
			}

			$coordinates = $geocodingService->geocode($address);

			if ($coordinates) {
				$user->setLatitude($coordinates['latitude']);
				$user->setLongitude($coordinates['longitude']);
				$entityManager->persist($user);
				$this->addFlash('success', "Coordonnées mises à jour pour l'utilisateur ID {$user->getId()}.");
			} else {
				$this->addFlash('warning', "Impossible de géocoder l'adresse pour l'utilisateur ID {$user->getId()}.");
			}

			// Pause pour respecter les politiques de Nominatim (1 requête par seconde)
			sleep(1);
		}
		if ($missingAddress) {
			$this->addFlash('warning', "Un ou plusieurs utilisateurs n'ont pas d'adresse postale.");
		}

		$entityManager->flush();

		return $this->redirectToRoute('app_syma_business'); // Assurez-vous que cette route existe
	}
}
