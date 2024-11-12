<?php

namespace App\Controller\Management;

use App\Entity\User;
use App\Entity\CarteSim;
use App\Entity\Commande;
use App\Entity\LignesCommande;
use App\Form\ClientSelectType;
use App\Repository\UserRepository;
use App\Repository\CarteSimRepository;
use App\Repository\ChapeletRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VentesComptoirApiController extends AbstractController
{
	#[Route('/management/select-client', name: 'select_client')]
	public function selectClient(Request $request, Security $security): Response
	{
		$form = $this->createForm(ClientSelectType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$selectedClient = $form->get('client')->getData();
			$user = $security->getUser(); // Utilisation du service injecté dans la méthode

			// Redirection vers biper_commande_comptoir avec l'ID utilisateur
			return $this->redirectToRoute('biper_commande_comptoir', ['clientId' => $selectedClient->getId()]);
		}

		return $this->render('interfaces_admin/select_client.html.twig', [
			'form' => $form->createView(),
		]);
	}
	#[Route('/management/list/commande/comptoir/{clientId}', name: 'biper_commande_comptoir')]
	public function showBiperCommande(string $clientId): Response
	{
		return $this->render('interfaces_admin/ventes_comptoir.html.twig', [
			'clientId' => $clientId, // Passer le client ID à la vue
		]);
	}
	#[Route('/api/comptoir/{clientId}', name: 'api_comptoir', methods: ['POST'])]
	public function comptoirChaplet(
		CommandeRepository $commandeRepository,
		Request $request,
		string $clientId,
		CarteSimRepository $carteSimRepo,
		ChapeletRepository $chapeletRepo,
		EntityManagerInterface $entityManager,
		UserRepository $userRepo // Ajouter le repository User
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

		// Récupérer la dernière commande pour obtenir son numéro
		$lastCommande = $commandeRepository->findOneBy([], ['id' => 'DESC']);
		$lastNumero = $lastCommande ? $lastCommande->getNumero() : 'CDE_000000';

		// Générer un nouveau numéro de commande
		$newCode = 'CDE_' . str_pad((int)substr($lastNumero, 4) + 1, 6, '0', STR_PAD_LEFT);

		// Créer une nouvelle commande
		$commande = new Commande();
		$commande->setNumero($newCode);  // Attribuer le nouveau numéro de commande
		$commande->setCodeClient($client->getCodeClient());
		$commande->setSimType($chapelet->getTypeCartes()->getNom());
		$commande->setTypeSim($chapelet->getTypeCartes());
		$commande->setQte(count($cartesSim));
		$commande->setQtevalidee(count($cartesSim));
		$commande->setTotal((count($cartesSim)) * ($chapelet->getTypeCartes()->getPrix()));
		$commande->setStatus('validee');
		$commande->setCreatedAt(new \DateTimeImmutable());
		$commande->setUser($client);

		$entityManager->persist($commande);

		// Création des lignes de commande pour les cartes SIM
		foreach ($cartesSim as $carteSim) {
			$this->createLigneCommande($entityManager, $carteSim, $commande, $client); // Passer le client aussi
		}

		// Marquer le chapelet comme réservé
		$chapelet->setReserved(true);
		$entityManager->persist($chapelet);

		// Enregistrer les modifications dans la base de données
		$entityManager->flush();

		return $this->json(['success' => 'Commande créée et chapelet réservé avec succès.']);
	}

	private function createLigneCommande(EntityManagerInterface $entityManager, CarteSim $carteSim, Commande $commande, User $client): void
	{
		$ligneCommande = new LignesCommande();
		$ligneCommande->setCartesSims($carteSim);
		$ligneCommande->setSerialNumber($carteSim->getSerialNumber());
		$ligneCommande->setCommande($commande);
		$ligneCommande->setNumeroCommande($commande->getNumero());
		$ligneCommande->setPrixUnitaire($carteSim->getType()->getPrix());
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
}
