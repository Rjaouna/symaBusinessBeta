<?php

namespace App\Controller\Management;

use App\Entity\Chapelet;
use App\Entity\CarteSim;
use App\Entity\SimType;
use App\Repository\ChapeletRepository;
use App\Repository\CarteSimRepository;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ChapeletController extends AbstractController
{
	// Méthode pour afficher le formulaire de scan du chapelet
	#[Route('/scan-chapelet', name: 'scan_chapelet')]
	public function scanChapeletView(SimTypeRepository $simTypeRepository)
	{
		$simTypes = $simTypeRepository->findAll();

		return $this->render('chapelet/scan.html.twig', [
			'simTypes' => $simTypes,
		]);
	}

	// Méthode API pour traiter le scan du chapelet
	#[Route('/api/scan-chapelet', name: 'api_scan_chapelet', methods: ['POST'])]
	public function scanChapelet(
		Request $request,
		ChapeletRepository $chapeletRepository,
		CarteSimRepository $carteSimRepository,
		SimTypeRepository $simTypeRepository,
		EntityManagerInterface $entityManager
	): JsonResponse {
		$data = json_decode($request->getContent(), true);
		$codeChapelet = $data['codeChapelet'] ?? null;

		if (!$codeChapelet) {
			return new JsonResponse(['error' => 'Le code du chapelet est requis.'], JsonResponse::HTTP_BAD_REQUEST);
		}
		// Vérifier que le codeChapelet commence par 'SBL' suivi de 11 chiffres
		if (!preg_match('/^SBL\d{11}$/', $codeChapelet)) {
			return new JsonResponse(
				['error' => 'Le code du chapelet doit commencer par "SBL" suivi de 11 chiffres.'],
				JsonResponse::HTTP_BAD_REQUEST
			);
		}


		// Vérifier si le chapelet existe déjà
		$existingChapelet = $chapeletRepository->findOneBy(['codeChapelet' => $codeChapelet]);

		if ($existingChapelet) {
			return new JsonResponse(['message' => 'Ce chapelet existe déjà dans la base de données.'], JsonResponse::HTTP_CONFLICT);
		}

		// Récupération du type de carte
		$typeCarteId = $data['typeCarteId'] ?? null;
		if ($typeCarteId) {
			$simType = $simTypeRepository->find($typeCarteId);
			if ($simType) {
				// Le type de carte est valide
			} else {
				return new JsonResponse(['error' => 'Type de carte invalide.'], JsonResponse::HTTP_BAD_REQUEST);
			}
		} else {
			return new JsonResponse(['error' => 'Le type de carte est requis.'], JsonResponse::HTTP_BAD_REQUEST);
		}

		// Créer un nouveau chapelet
		$chapelet = new Chapelet();
		$chapelet->setCodeChapelet($codeChapelet);
		$chapelet->setReserved(false);
		$chapelet->setTypeCartes($simType);

		$entityManager->persist($chapelet);

		// Générer 5 cartes associées
		$cartesSim = [];
		for ($i = 0; $i < 5; $i++) {
			$uniqueSerialNumber = $this->generateUniqueSerialNumber($carteSimRepository);

			if (!$uniqueSerialNumber) {
				return new JsonResponse(['error' => 'Impossible de générer un numéro de série unique.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
			}

			$carteSim = new CarteSim();
			$carteSim->setSerialNumber($uniqueSerialNumber);
			$carteSim->setReserved(false);
			$carteSim->setType($simType);
			$carteSim->setChapelet($chapelet);

			$entityManager->persist($carteSim);
			$cartesSim[] = $carteSim;
		}

		// Enregistrer les entités dans la base de données
		$entityManager->flush();

		// Retourner une réponse JSON
		return new JsonResponse([
			'message' => 'Chapelet et cartes créés avec succès.',
			'chapelet' => [
				'id' => $chapelet->getId(),
				'codeChapelet' => $chapelet->getCodeChapelet(),
			],
			'cartes' => array_map(function (CarteSim $carteSim) {
				return [
					'id' => $carteSim->getId(),
					'serialNumber' => $carteSim->getSerialNumber(),
				];
			}, $cartesSim),
		], JsonResponse::HTTP_CREATED);
	}

	// Fonction pour générer un numéro de série unique de 19 chiffres
	private function generateUniqueSerialNumber(CarteSimRepository $carteSimRepository): ?string
	{
		$attempts = 0;
		$maxAttempts = 10;

		do {
			$serialNumber = $this->generateRandomNumber(19);

			$existingCarteSim = $carteSimRepository->findOneBy(['serialNumber' => $serialNumber]);

			if (!$existingCarteSim) {
				return $serialNumber;
			}

			$attempts++;
		} while ($attempts < $maxAttempts);

		return null; // Retourne null si un numéro unique n'a pas pu être généré après plusieurs tentatives
	}

	// Fonction pour générer un nombre aléatoire de N chiffres
	private function generateRandomNumber(int $length): string
	{
		$digits = '0123456789';
		$number = '';
		for ($i = 0; $i < $length; $i++) {
			$number .= $digits[rand(0, 9)];
		}
		return $number;
	}
}
