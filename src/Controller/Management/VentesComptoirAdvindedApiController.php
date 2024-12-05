<?php

namespace App\Controller\Management;

use App\Entity\User;
use App\Entity\Usage;
use App\Entity\CarteSim;
use App\Entity\Commande;
use App\Entity\LignesCommande;
use App\Entity\Limitation;
use App\Repository\UserRepository;
use App\Repository\UsageRepository;
use App\Form\ClientSelectAdvincedType;
use App\Repository\CarteSimRepository;
use App\Repository\ChapeletRepository;
use App\Repository\CommandeRepository;
use App\Repository\LignesCommandeRepository;
use App\Repository\LimitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted("ROLE_ADMIN or ROLE_COMMERCIAL")]
class VentesComptoirAdvindedApiController extends AbstractController
{
	#[Route('/advinced/select-client', name: 'select_advinced_client')]
	public function selectClient(Request $request, Security $security): Response
	{
		$form = $this->createForm(ClientSelectAdvincedType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$selectedClient = $form->get('client')->getData();
			return $this->redirectToRoute('advinced_biper_commande_comptoir', ['clientId' => $selectedClient->getId()]);
		}

		return $this->render('interfaces_admin/advinced/select_client.html.twig', [
			'form' => $form->createView(),
		]);
	}

	#[Route('/commercial/advinced/select-client', name: 'select_advinced_client_for_commercial')]
	public function selectClientForCommercial(Request $request, Security $security): Response
	{
		$form = $this->createForm(ClientSelectAdvincedType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$selectedClient = $form->get('client')->getData();
			return $this->redirectToRoute('advinced_biper_commande_comptoir', ['clientId' => $selectedClient->getId()]);
		}

		return $this->render('interfaces_commercial/select_client.html.twig', [
			'form' => $form->createView(),
		]);
	}

	#[Route('/management/advinced/list/commande/comptoir/s45{clientId}214', name: 'advinced_biper_commande_comptoir')]
	public function showBiperCommande(string $clientId, UserRepository $userRepo, CommandeRepository $commandeRepository): Response
	{
		$client = $userRepo->find($clientId);

		if (!$client) {
			throw $this->createNotFoundException('Client introuvable.');
		}
		// Récupérer les commandes du client
		$commandes = $commandeRepository->findBy(['user' => $client], ['createdAt' => 'DESC']);

		return $this->render('interfaces_admin/advinced/ventes_comptoir.html.twig', [
			'clientId' => $clientId,
			'client' => $client,
			'commandes' => $commandes
		]);
	}

	#[Route('/advinced/api/comptoir/{clientId}', name: 'advinced_api_comptoir', methods: ['POST'])]
	public function comptoirChaplet(
		CommandeRepository $commandeRepository,
		Request $request,
		string $clientId,
		CarteSimRepository $carteSimRepo,
		ChapeletRepository $chapeletRepo,
		EntityManagerInterface $entityManager,
		UserRepository $userRepo,
		UsageRepository $usageRepository,
		LimitationRepository $limitationRepository
	): JsonResponse {
		$data = json_decode($request->getContent(), true);
		$chapeletCode = $data['chapeletCode'] ?? null;

		if (!$chapeletCode) {
			return $this->json(['error' => 'Le code chapelet est requis.'], 400);
		}

		// Recherche du chapelet par son code
		$chapelet = $chapeletRepo->findOneBy(['codeChapelet' => $chapeletCode]);

		if (!$chapelet) {
			return $this->json(['error' => 'Le chapelet est introuvable.'], 404);
		}

		// Vérifier si le chapelet est réservé
		if ($chapelet->isReserved()) {
			return $this->json(['error' => 'Le chapelet est déjà réservé.'], 400);
		}

		// Récupérer l'utilisateur correspondant à l'ID du client
		$client = $userRepo->findOneBy(['id' => $clientId]);

		if (!$client) {
			return $this->json(['error' => 'Client introuvable.'], 404);
		}

		// Récupérer les cartes SIM associées au chapelet
		$cartesSim = $carteSimRepo->findBy([
			'chapelet' => $chapelet,
			'reserved' => false,
		]);

		// Vérifier si toutes les 5 cartes SIM sont libres
		if (count($cartesSim) !== 5) {
			return $this->json(['error' => 'Le chapelet ne contient pas 5 cartes SIM libres.'], 400);
		}

		// Étape 1 : Récupérer l'usage actuel de l'utilisateur
		$usage = $usageRepository->findOneBy([
			'user' => $client,
			'type' => $chapelet->getTypeCartes(),
		]);

		$currentUsage = $usage ? $usage->getConsomation() : 0;
		$additionalUsage = count($cartesSim); // Nombre de cartes SIM achetées
		$newUsage = $currentUsage + $additionalUsage;

		// Étape 2 : Récupérer les limitations pour le type de carte SIM
		$limitations = $limitationRepository->findBy(
			['typeCarte' => $chapelet->getTypeCartes()],
			['limite' => 'ASC']
		);

		// Étape 3 : Calculer le prix total en fonction des limitations
		$totalPrice = $this->calculateTotalPrice(
			$currentUsage,
			$additionalUsage,
			$limitations,
			$chapelet->getTypeCartes()->getPrix()
		);

		// Générer un nouveau numéro de commande
		$lastCommande = $commandeRepository->findOneBy([], ['id' => 'DESC']);
		$lastNumero = $lastCommande ? $lastCommande->getNumero() : 'CDE_000000';
		$newCode = 'CDE_' . str_pad((int)substr($lastNumero, 4) + 1, 6, '0', STR_PAD_LEFT);

		// Créer une nouvelle commande
		$commande = new Commande();
		$commande->setNumero($newCode);  // Attribuer le nouveau numéro de commande
		$commande->setCodeClient($client->getCodeClient());
		$commande->setSimType($chapelet->getTypeCartes()->getNom());
		$commande->setTypeSim($chapelet->getTypeCartes());
		$commande->setQte(count($cartesSim));
		$commande->setQtevalidee(count($cartesSim));
		$commande->setTotal($totalPrice); // Utiliser le prix total calculé
		$commande->setMontantHt($totalPrice); // Vous pouvez ajuster en fonction de la TVA
		$commande->setStatus('validee');
		$commande->setCreatedAt(new \DateTimeImmutable());
		$commande->setUser($client);

		$entityManager->persist($commande);

		// Création des lignes de commande pour les cartes SIM
		foreach ($cartesSim as $carteSim) {
			$this->createLigneCommande($entityManager, $carteSim, $commande, $client, $limitations, $currentUsage);
			$currentUsage++; // Incrémenter l'usage pour chaque carte SIM traitée
		}

		// Mettre à jour l'usage de l'utilisateur
		if ($usage) {
			$usage->setConsomation($newUsage);
		} else {
			$usage = new Usage();
			$usage->setUser($client);
			$usage->setType($chapelet->getTypeCartes());
			$usage->setConsomation($newUsage);
			$entityManager->persist($usage);
		}

		// Marquer le chapelet comme réservé
		$chapelet->setReserved(true);
		$entityManager->persist($chapelet);

		// Enregistrer les modifications dans la base de données
		$entityManager->flush();

		return $this->json(['success' => 'Commande créée et chapelet réservé avec succès.']);
	}

	/**
	 * Fonction pour calculer le prix total en fonction des limitations
	 */
	private function calculateTotalPrice($currentUsage, $additionalUsage, $limitations, $defaultPrice)
	{
		$totalPrice = 0;
		$remainingUnits = $additionalUsage;
		$usage = $currentUsage;

		foreach ($limitations as $limitation) {
			if ($usage >= $limitation->getLimite()) {
				continue; // Passer si la limite est déjà atteinte
			}

			$unitsAtThisPrice = min($limitation->getLimite() - $usage, $remainingUnits);
			$totalPrice += $unitsAtThisPrice * $limitation->getPrix();

			$usage += $unitsAtThisPrice;
			$remainingUnits -= $unitsAtThisPrice;

			if ($remainingUnits <= 0) {
				break;
			}
		}

		// Appliquer le prix par défaut pour les unités restantes
		if ($remainingUnits > 0) {
			$totalPrice += $remainingUnits * $defaultPrice;
		}

		return $totalPrice;
	}

	private function createLigneCommande(
		EntityManagerInterface $entityManager,
		CarteSim $carteSim,
		Commande $commande,
		User $client,
		$limitations,
		$currentUsage
	): void {
		// Déterminer le prix unitaire pour cette carte SIM
		$prixUnitaire = $this->determinePrixUnitaire($currentUsage, $limitations, $carteSim->getType()->getPrix());
		$currentUsage++; // Incrémenter l'usage après chaque carte SIM

		$ligneCommande = new LignesCommande();
		$ligneCommande->setCartesSims($carteSim);
		$ligneCommande->setSerialNumber($carteSim->getSerialNumber());
		$ligneCommande->setCommande($commande);
		$ligneCommande->setNumeroCommande($commande->getNumero());
		$ligneCommande->setPrixUnitaire($prixUnitaire);
		$ligneCommande->setTypeSim($carteSim->getType());
		$ligneCommande->setCreatedAt(new \DateTimeImmutable());

		// Marquer la carte SIM comme réservée et l'associer au client
		$carteSim->setReserved(true);
		$carteSim->setPurchasedBy($client); // Associer la carte SIM au client
		$carteSim->setUser($client); // Associer le client à la carte SIM
		$carteSim->setCanalVente('Vente Comptoir');

		$entityManager->persist($ligneCommande);
		$entityManager->persist($carteSim);
	}

	/**
	 * Fonction pour déterminer le prix unitaire en fonction de l'usage actuel
	 */
	private function determinePrixUnitaire($usage, $limitations, $defaultPrice)
	{
		foreach ($limitations as $limitation) {
			if ($usage < $limitation->getLimite()) {
				return $limitation->getPrix();
			}
		}

		// Si aucune limitation ne s'applique, retourner le prix par défaut
		return $defaultPrice;
	}
}