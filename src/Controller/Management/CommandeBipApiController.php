<?php

namespace App\Controller\Management;

use App\Entity\CarteSim;
use App\Entity\Commande;
use App\Entity\LignesCommande;
use App\Repository\CarteSimRepository;
use App\Repository\ChapeletRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeBipApiController extends AbstractController
{
	#[Route('/management/list/commande/{clientId}', name: 'biper_commande')]
	public function showBiperCommande(string $clientId): Response
	{
		return $this->render('interfaces_admin/commandes_validation_api.html.twig', [
			'clientId' => $clientId,
		]);
	}

	#[Route('/api/commandes/{clientId}', name: 'api_commandes_client', methods: ['GET'])]
	public function getCommandesClient(string $clientId, CommandeRepository $commandeRepo): JsonResponse
	{
		// Récupération des commandes avec le statut en_cours ou en_attente
		$commandesClient = $commandeRepo->findBy([
			'code_client' => $clientId,
			'status' => ['en_cours', 'en_attente']
		]);

		// Transformation des données en format de réponse JSON
		$commandesData = [];
		foreach ($commandesClient as $commande) {
			$commandesData[] = [
				'id' => $commande->getId(),
				'user' => $commande->getUser()->getNomResponsable(),
				'code_client' => $commande->getCodeClient(),
				'typeCarte' => $commande->getSimType(),
				'nomClient' => $commande->getUser(),
				'numero' => $commande->getNumero(),
				'qte' => $commande->getQte(),
				'qtevalidee' => $commande->getQtevalidee(),
				'status' => $commande->getStatus(),
				'date' => $commande->getCreatedAt()->format('Y-m-d H:i:s'),
			];
		}

		// Retourner la réponse JSON
		return new JsonResponse($commandesData);
	}

	#[Route('/advinced/api/commandes/{clientId}', name: 'advinced_api_commandes_client', methods: ['GET'])]
	public function getAdvincedCommandesClient($clientId, CommandeRepository $commandeRepo): JsonResponse
	{
		// Récupération des commandes avec le statut en_cours ou en_attente
		$commandesClient = $commandeRepo->findBy([
			'user' => $clientId,'factured' => 0,
			'soldBy' => $this->getUser()->getNomResponsable(),

		]);

		// Transformation des données en format de réponse JSON
		$commandesData = [];
		foreach ($commandesClient as $commande) {
			$commandesData[] = [
				'id' => $commande->getId(),
				'user' => $commande->getUser()->getNomResponsable(),
				'code_client' => $commande->getCodeClient(),
				'typeCarte' => $commande->getSimType(),
				'nomClient' => $commande->getUser(),
				'numero' => $commande->getNumero(),
				'qte' => $commande->getQte(),
				'qtevalidee' => $commande->getQtevalidee(),
				'status' => $commande->getStatus(),
				'total' => $commande->getTotal(),
				'date' => $commande->getCreatedAt()->format('Y-m-d H:i:s'),
			];
		}

		// Retourner la réponse JSON
		return new JsonResponse($commandesData);
	}



	#[Route('/advinced/api/commandes/{clientId}/facturer', name: 'advinced_api_commandes_facturer', methods: ['POST'])]
	public function facturerCommandesClient(
		$clientId,
		CommandeRepository $commandeRepo,
		EntityManagerInterface $entityManager
	): JsonResponse {
		// Récupérer les commandes du client avec le statut approprié
		$commandesClient = $commandeRepo->createQueryBuilder('c')
		->where('c.user = :clientId')
		->andWhere('c.factured != :statutFacture')
			->andWhere('c.soldBy = :commercial')
		->setParameter('clientId', $clientId)
			->setParameter('commercial', $this->getUser()->getNomResponsable())
			->setParameter('statutFacture', 1)
			->getQuery()
			->getResult();

		if (empty($commandesClient)) {
			return new JsonResponse(['message' => 'Aucune commande à facturer pour ce client.'], JsonResponse::HTTP_OK);
		}

		// Mettre à jour le statut de chaque commande
		foreach ($commandesClient as $commande) {
			$commande->setFactured(1);
		}

		// Enregistrer les changements
		$entityManager->flush();

		return new JsonResponse(['message' => 'Commandes facturées avec succès.'], JsonResponse::HTTP_OK);
	}


	#[Route('/api/biper/{clientId}', name: 'api_biper', methods: ['POST'])]
	public function biperChaplet(
		Request $request,
		string $clientId,
		CarteSimRepository $carteSimRepo,
		ChapeletRepository $chapeletRepo,
		CommandeRepository $commandeRepo,
		EntityManagerInterface $entityManager
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

		// Récupérer le type de cartes SIM du chapelet
		$simType = $chapelet->getTypeCartes();  // Retourne un objet SimType

		// Vérifier si une commande en cours ou en attente existe pour ce client et ce type de SIM
		$commande = $commandeRepo->findOneBy([
			'code_client' => $clientId,
			'TypeSim' => $simType,  // Utilisation de la propriété TypeSim
			'status' => ['en_cours', 'en_attente']  // Vérification des statuts
		]);


		if (!$commande) {
			return $this->json(['error' => 'Aucune commande en cours ou en attente pour ce client.'], 404);
		}

		// Vérifier si le chapelet est réservé
		if ($chapelet->isReserved()) {
			return $this->json(['error' => 'Le chapelet est déjà réservé.'], 400);
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

		// Création des lignes de commande pour les cartes SIM
		foreach ($cartesSim as $carteSim) {
			$this->createLigneCommande($entityManager, $carteSim, $commande);
		}

		// Mise à jour du statut de la commande
		$this->updateCommande($commande, count($cartesSim));

		// Marquer le chapelet comme réservé
		$chapelet->setReserved(true);
		$entityManager->persist($chapelet);

		// Enregistrer les modifications dans la base de données
		$entityManager->flush();

		return $this->json(['success' => 'Chapelet réservé, les cartes SIM associées ont été bien réservées.']);
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

		// Marquer la carte SIM comme réservée
		$carteSim->setReserved(true);
		$carteSim->setPurchasedBy($commande->getUser());
		$carteSim->setUser($commande->getUser());
		$carteSim->setCanalVente('Vente via Application');


		$entityManager->persist($ligneCommande);
		$entityManager->persist($carteSim);
	}

	private function updateCommande(Commande $commande, int $nbCartes): void
	{
		// Mise à jour de la quantité validée de la commande
		$commande->setQtevalidee($commande->getQtevalidee() + $nbCartes);

		// Vérifier si la commande est entièrement validée
		if ($commande->getQtevalidee() >= $commande->getQte()) {
			$commande->setStatus('validee');
		} else {
			$commande->setStatus('en_cours');
		}
	}

}
