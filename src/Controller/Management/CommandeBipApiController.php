<?php
// src/Controller/Management/CommandeBipApiController.php

namespace App\Controller\Management;

use App\Entity\CarteSim;
use App\Entity\Commande;
use App\Entity\LignesCommande;
use App\Repository\CarteSimRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeBipApiController extends AbstractController
{

	#[Route('/biper/commande/{clientId}', name: 'biper_commande')]
	public function showBiperCommande(string $clientId): Response
	{
		return $this->render('interfaces_admin/commandes_validation_api.html.twig', [
			'clientId' => $clientId, // Passer le client ID à la vue
		]);
	}


	#[Route('/api/biper/{clientId}', name: 'api_biper', methods: ['POST'])]
	public function biperSerialNumber(
		Request $request,
		string $clientId,
		CarteSimRepository $carteSimRepo,
		CommandeRepository $commandeRepo,
		EntityManagerInterface $entityManager
	): JsonResponse {
		// Récupérer le contenu JSON de la requête
		$data = json_decode($request->getContent(), true);

		$serialNumber = $data['serialNumber'] ?? null;

		if (!$serialNumber) {
			return $this->json(['error' => 'Le numéro de série est requis.'], 400);
		}

		// Vérification de la carte SIM
		$carteSim = $this->getCarteSim($carteSimRepo, $serialNumber);
		if (!$carteSim) {
			return $this->json(['error' => 'Carte SIM non trouvée.'], 404);
		}

		// Vérification si la carte SIM est déjà réservée
		if ($carteSim->isReserved()) {
			return $this->json(['error' => 'Cette carte SIM a déjà été réservée ou achetée.'], 400);
		}

		// Récupérer la commande pour le client
		$commande = $this->getCommande($commandeRepo, $clientId, $carteSim->getType());
		if (!$commande) {
			return $this->json(['error' => 'Aucune commande en cours pour ce client.'], 404);
		}

		// Mettre à jour la commande et créer une nouvelle ligne de commande
		$this->updateCommande($commande);
		$this->createLigneCommande($entityManager, $carteSim, $commande);

		return $this->json(['success' => 'Ligne de commande créée avec succès et carte SIM réservée.']);
	}

	private function getCarteSim(CarteSimRepository $carteSimRepo, string $serialNumber): ?CarteSim
	{
		return $carteSimRepo->findOneBy(['serialNumber' => $serialNumber]);
	}

	private function getCommande(CommandeRepository $commandeRepo, string $clientId, $typeCarteSim): ?Commande
	{
		$commandeEnCours = $commandeRepo->findOneBy([
			'code_client' => $clientId,
			'status' => 'en_cours',
			'simType' => $typeCarteSim->getNom(),
		], ['createdAt' => 'ASC']);

		if (!$commandeEnCours) {
			return $commandeRepo->findOneBy([
				'code_client' => $clientId,
				'status' => 'en_attente',
				'simType' => $typeCarteSim->getNom(),
			], ['createdAt' => 'ASC']);
		}

		return $commandeEnCours;
	}

	private function updateCommande(Commande $commande): void
	{
		$commande->setQtevalidee($commande->getQtevalidee() + 1);

		if ($commande->getQtevalidee() >= $commande->getQte()) {
			$commande->setStatus('validee');
		} else {
			$commande->setStatus('en_cours');
		}
	}

	private function createLigneCommande(EntityManagerInterface $entityManager, CarteSim $carteSim, Commande $commande): void
	{
		$ligneCommande = new LignesCommande();
		$ligneCommande->setCartesSims($carteSim);
		$ligneCommande->setSerialNumber($carteSim->getSerialNumber());
		$ligneCommande->setCommande($commande);
		$ligneCommande->setNumeroCommande($commande->getNumero());
		$ligneCommande->setPrixUnitaire($carteSim->getType()->getPrix());
		$ligneCommande->setTypeSim($carteSim->getType());

		// Mise à jour de la carte SIM comme réservée
		$carteSim->setReserved(true);
		$carteSim->setPurchasedBy($commande->getUser());
		$carteSim->setUser($commande->getUser());

		// Persist des entités
		$entityManager->persist($ligneCommande);
		$entityManager->persist($carteSim);
		$entityManager->flush();
	}
}
