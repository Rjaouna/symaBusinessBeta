<?php
// src/Controller/Management/CommandeLigneController.php

namespace App\Controller\Management;

use App\Entity\CarteSim;
use App\Entity\Commande;
use App\Entity\LignesCommande;
use App\Repository\CarteSimRepository;
use App\Repository\CommandeRepository;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class CommandeLigneAutoValidatorController extends AbstractController
{
	#[Route('/management/commande/{numeroCommande}/ajouter-lignes', name: 'Commande_Ligne_Auto_Validato', methods: ['GET'])]
	public function ajouterLignesCommande(
		string $numeroCommande,
		CommandeRepository $commandeRepo,
		CarteSimRepository $carteSimRepo,
		EntityManagerInterface $entityManager,
		SimTypeRepository $simTypeRepository
	): Response {
		// Récupérer la commande par numéro
		$commande = $commandeRepo->findOneBy(['numero' => $numeroCommande]);

		if (!$commande) {
			$this->addFlash('error', 'Commande introuvable.');
			return $this->redirectToRoute('users_with_orders');
		}
		$quantite = $commande->getQte();
		$typeSim = $simTypeRepository->findBy(['nom' => $commande->getSimType()]);

		// Récupérer des cartes SIM disponibles (non réservées) pour la quantité demandée
		$cartesDisponibles = $carteSimRepo->findBy(['reserved' => 0, 'type' => $typeSim], null, $quantite);
		if (count($cartesDisponibles) < $quantite) {
			$this->addFlash('error', 'Pas assez de cartes SIM disponibles pour cette commande.');
			return $this->redirectToRoute('users_with_orders');
		}

		foreach ($cartesDisponibles as $carteSim) {
			// Créer une nouvelle ligne de commande
			$ligneCommande = new LignesCommande();
			$ligneCommande->setCartesSims($carteSim);
			$ligneCommande->setSerialNumber($carteSim->getSerialNumber());
			$ligneCommande->setCommande($commande);
			$ligneCommande->setNumeroCommande($commande->getNumero());
			$ligneCommande->setPrixUnitaire($carteSim->getType()->getPrix());
			$ligneCommande->setTypeSim($carteSim->getType());

			// Marquer la carte SIM comme réservée
			$carteSim->setReserved(true);
			$carteSim->setUser($commande->getUser());

			// Persister les entités
			$entityManager->persist($ligneCommande);
			$entityManager->persist($carteSim);
		}

		// Mettre à jour la quantité validée dans la commande
		$commande->setQtevalidee($commande->getQtevalidee() + $quantite);
		$commande->setStatus('validee');
		$entityManager->persist($commande);

		// Appliquer les modifications
		$entityManager->flush();


		$this->addFlash('success', 'Lignes de commande ajoutées avec succès.');
		return $this->redirectToRoute('users_with_orders');
	}
}
