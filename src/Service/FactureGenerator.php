<?php
// src/Service/FactureGenerator.php
namespace App\Service;

use App\Entity\Facture;
use App\Entity\LigneFacture;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

class FactureGenerator
{
	public function __construct(
		private EntityManagerInterface $entityManager,
		private CommandeRepository $commandeRepository
	) {}

	public function genererFactures(): array
	{
		// Récupérer les commandes groupées par client et type de carte SIM
		$groupedData = $this->commandeRepository->findNonFacturedGroupedByClientAndSimType();

		$factures = [];

		foreach ($groupedData as $data) {
			$clientId = $data['clientId'];

			// Vérifiez si une facture existe déjà pour ce client
			if (!isset($factures[$clientId])) {
				// Création de la facture
				$facture = new Facture();
				$facture->setNumeroFacture('FAC_' . uniqid());
				$facture->setClient($this->entityManager->getReference('App\Entity\User', $clientId));
				$facture->setCreatedAt(new \DateTimeImmutable());
				$facture->setStatutPaiement('en-attente');
				$facture->setModePaiement('espece');

				$factures[$clientId] = $facture;
				$this->entityManager->persist($facture);
			} else {
				$facture = $factures[$clientId];
			}

			// Calcul du prix unitaire HT et du montant total HT
			if ($data['totalQuantity'] > 0) {
				$prixUnitaireHT = $data['totalPrice'] / $data['totalQuantity'];
			} else {
				// Si la quantité est 0, on peut choisir de ne pas générer cette ligne ou définir un prix par défaut
				$prixUnitaireHT = 0;
			}

			// Vérifiez si le prix unitaire HT est valide avant de continuer
			if ($prixUnitaireHT <= 0) {
				continue; // Si le prix unitaire est invalide, on passe à la commande suivante
			}

			// Création de la ligne de facture
			$ligneFacture = new LigneFacture();
			$ligneFacture->setFacture($facture);
			$ligneFacture->setTypeCarteSim($data['simTypeNom']);
			$ligneFacture->setQuantite($data['totalQuantity']);
			$ligneFacture->setPrixUnitaireHT($prixUnitaireHT);
			$ligneFacture->setMontantTotalHT($data['totalPrice']);

			$this->entityManager->persist($ligneFacture);
		}

		// Sauvegarde des modifications
		$this->entityManager->flush();

		return ['success' => 'Les factures ont été générées avec succès.'];
	}
}
