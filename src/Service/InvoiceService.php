<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Facture;
use App\Entity\LigneFacture;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceService
{
	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	// Générer la facture pour un utilisateur spécifique
	public function generateInvoiceForUser(User $client)
	{
		$totalMontant = 0;

		// Les types de cartes et leurs prix
		$types = [
			'Sim5' => ['quantity' => $client->getSim5Bonus(), 'price' => 4.99],
			'Sim10' => ['quantity' => $client->getSim10Bonus(), 'price' => 9.99],
			'Sim15' => ['quantity' => $client->getSim15Bonus(), 'price' => 14.99],
			'Sim20' => ['quantity' => $client->getSim20Bonus(), 'price' => 19.99],
		];

		// Vérification : générer l'avoir uniquement s'il y a au moins une quantité non nulle
		$hasValue = array_reduce($types, fn($carry, $data) => $carry || $data['quantity'] > 0, false);


		if (!$hasValue) {
			// Si toutes les quantités sont nulles ou égales à zéro, on ne génère pas d'avoir
			return;
		}

		// Créer la facture
		$facture = new Facture();
		$facture->setNumeroFacture('AVV_' . uniqid());
		$facture->setClient($client);

		// Créer des lignes de facture pour chaque type de carte SIM
		foreach ($types as $type => $data) {
			if ($data['quantity'] > 0) {
				$ligneFacture = new LigneFacture();

				// Assigner le type de carte SIM à la ligne de facture
				$ligneFacture->setTypeCarteSim($type);

				// Assigner la quantité
				$ligneFacture->setQuantite($data['quantity'] * 5);

				// Assigner le prix unitaire HT
				$prixUnitaireHT = $data['price'];
				$ligneFacture->setPrixUnitaireHT($prixUnitaireHT);

				// Calculer le montant total HT pour cette ligne
				$montantTotalHT = $prixUnitaireHT * $data['quantity'];
				$ligneFacture->setMontantTotalHT(($montantTotalHT * ($data['quantity'] * 5)) * -1);

				// Ajouter la ligne de facture à la facture
				$facture->addLigneFacture($ligneFacture);

				// Ajouter le montant HT de cette ligne au total
				$totalMontant += $montantTotalHT;
			}
		}

		// Calcul du montant de la TVA et du montant TTC
		$facture->setMontantHT(($totalMontant * 5) * -1);
		$facture->setMontantTVA($totalMontant * 0.2 * 5); // Par exemple, TVA à 20%
		$facture->setMontantTTC(($totalMontant * 1.2 * 5) * -1); // Montant TTC
		$facture->setType('AVV');
		$facture->setStatutPaiement('Non payé');
		$facture->setModePaiement('Non défini');
		$facture->setPaiementAt(new \DateTimeImmutable());

		// Sauvegarder la facture dans la base de données
		$this->entityManager->persist($facture);
		$this->entityManager->flush();
	}

	// Générer des factures pour tous les utilisateurs
	public function generateInvoicesForAllUsers()
	{
		// Récupérer tous les utilisateurs
		$users = $this->entityManager->getRepository(User::class)->findAll();

		foreach ($users as $user) {
			$this->generateInvoiceForUser($user);  // Appeler la méthode pour générer la facture pour chaque utilisateur
			// Réinitialiser les bonus de chaque utilisateur
			$user->setSim5Bonus(null);
			$user->setSim10Bonus(null);
			$user->setSim15Bonus(null);
			$user->setSim20Bonus(null);

			// Persister les modifications de l'utilisateur
			$this->entityManager->persist($user);
		}
		// Sauvegarder toutes les modifications en une seule transaction
		$this->entityManager->flush();
	}

}
