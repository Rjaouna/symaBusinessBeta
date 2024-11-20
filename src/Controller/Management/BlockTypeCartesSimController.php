<?php

namespace App\Controller\Management;

use App\Repository\CarteSimRepository;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/stock/cartes/sim')]
final class BlockTypeCartesSimController extends AbstractController
{
	#[Route(name: 'app_carte_sim_types_block_index', methods: ['GET'])]
	public function index(CarteSimRepository $carteSimRepository, CommandeRepository $commandeRepository): Response
	{
		// Récupérer toutes les commandes filtrées selon des critères
		$commandes = $commandeRepository->findBy(['status' => ['en_attente', 'en_cours']], ['id' => 'ASC']);

		// Initialiser un tableau pour stocker les résultats par type de carte
		$quantitesRestantes = [];

		foreach ($commandes as $commande) {
			$qteValidee = $commande->getQtevalidee() ?? 0;
			$quantiteRestante = $commande->getQte() - $qteValidee;
			$simType = $commande->getSimType();

			// Si le type de carte existe déjà dans le tableau, ajouter la différence
			$quantitesRestantes[$simType] = ($quantitesRestantes[$simType] ?? 0) + $quantiteRestante;
		}

		// Récupère le nombre de cartes SIM par type
		$carteSimsByType = $carteSimRepository->countByType();

		// Passer les données au template
		return $this->render('carte_sim/_block_sim_type.html.twig', [
			'carte_sims_by_type' => $carteSimsByType,
			'quantitesRestantes' => $quantitesRestantes
		]);
	}
}
