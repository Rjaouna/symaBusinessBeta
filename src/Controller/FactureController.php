<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Facture;
use App\Entity\LigneFacture;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class FactureController extends AbstractController
{
	private $commandeRepository;
	private $entityManager;
	private $userRepository;

	public function __construct(CommandeRepository $commandeRepository, EntityManagerInterface $entityManager, UserRepository $userRepository)
	{
		$this->commandeRepository = $commandeRepository;
		$this->entityManager = $entityManager;
		$this->userRepository = $userRepository;
	}

	#[Route('/generate-invoices-fac', name: 'generate_all_invoices_fac')]
	public function generateInvoices(): Response
	{
		// Vérifier si des commandes sont encore en état "en_cours"
		$commandesEnCours = $this->commandeRepository->findBy(['status' => 'en_cours']);

		if (count($commandesEnCours) > 0) {
			// Ajouter un message flash pour informer l'utilisateur
			$this->addFlash('warning', 'Il existe des commandes en cours. Veuillez les finaliser avant de générer les factures.');

			// Rediriger vers une autre page ou rester sur la page actuelle
			return $this->redirectToRoute('app_facture_list'); // Remplacez par une route appropriée
		}
		
		// Récupérer la date du début et de la fin du mois
		$startDate = (new DateTimeImmutable('first day of this month'))->setTime(0, 0, 0);
		$endDate = (new DateTimeImmutable('last day of this month'))->setTime(23, 59, 59);

		// Récupérer tous les utilisateurs (clients)
		$clients = $this->userRepository->findAll();

		foreach ($clients as $client) {
			// Récupérer toutes les commandes de ce client pour le mois donné
			$commandes = $this->commandeRepository->findCommandesByClientAndMonth($client->getId(), $startDate, $endDate);
			if (count($commandes) === 0) {
				// Passer au client suivant si aucune commande non facturée n'est trouvée
				continue;
			}
			// Cumuler les quantités et montants pour chaque type de carte SIM
			$simTypes = [];
			$totalMontant = 0;

			foreach ($commandes as $commande) {
				$simType = $commande->getTypeSim();
				if (!isset($simTypes[$simType->getId()])) {
					$simTypes[$simType->getId()] = [
						'qte' => 0,
						'montantHt' => 0,
						'simType' => $simType
					];
				}

				$simTypes[$simType->getId()]['qte'] += $commande->getQte();
				$simTypes[$simType->getId()]['montantHt'] += $commande->getTotal();
				$totalMontant += $commande->getTotal();
			}

			// Créer une facture pour ce client
			$facture = new Facture();
			$facture->setNumeroFacture('FAC_' . uniqid());
			$facture->setClient($client);
			$facture->setMontantHT($totalMontant);
			$facture->setMontantTVA($totalMontant * 0.2); // Par exemple, TVA à 20%
			$facture->setMontantTTC($totalMontant * 1.2); // Montant TTC
			$facture->setType('FAC');
			$facture->setStatutPaiement('Non payé');
			$facture->setModePaiement('Non défini');
			$facture->setPaiementAt(new DateTimeImmutable());

			// Créer des lignes de facture pour chaque type de carte SIM
			foreach ($simTypes as $simTypeData) {
				$ligneFacture = new LigneFacture();

				// Assigner la facture à la ligne de facture
				$ligneFacture->setFacture($facture);

				// Assigner le type de carte SIM
				$ligneFacture->setTypeCarteSim($simTypeData['simType']->getNom()); // Utiliser le nom ou une propriété du type de carte SIM

				// Assigner la quantité
				$ligneFacture->setQuantite($simTypeData['qte']);

				// Assigner le prix unitaire HT
				$prixUnitaireHT = $simTypeData['simType']->getPrix(); // On suppose que `getPrixUnitaireHT()` existe dans l'entité `SimType`
				$ligneFacture->setPrixUnitaireHT($prixUnitaireHT);

				// Calculer le montant total HT pour cette ligne
				$montantTotalHT = $prixUnitaireHT * $simTypeData['qte'];
				$ligneFacture->setMontantTotalHT($montantTotalHT);

				// Ajouter la ligne de facture à la facture
				$facture->addLigneFacture($ligneFacture);
			}


			// Sauvegarder la facture dans la base de données
			$this->entityManager->persist($facture);
			// Marquer toutes les commandes de ce client comme facturées
			foreach ($commandes as $commande) {
				$commande->setFactured(true);
				$this->entityManager->persist($commande);
			}
		}
			// Sauvegarder toutes les modifications
			$this->entityManager->flush();
		
		// Rediriger vers la page de génération de avoirs
		return $this->redirectToRoute('generate_all_invoices_avv');
	}
}