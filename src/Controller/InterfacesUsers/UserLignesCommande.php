<?php

namespace App\Controller\InterfacesUsers;

use App\Repository\CommandeRepository;
use App\Repository\LignesCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserLignesCommande extends AbstractController
{
	#[Route('/commandes/{numeroCommande}/lignes', name: 'lignes_commande_show')]
	public function showLignesCommande(string $numeroCommande, LignesCommandeRepository $lignesCommandeRepo, CommandeRepository $commandeRepo): Response
	{
		// Récupérer la commande par numéro
		$commande = $commandeRepo->findOneBy(['numero' => $numeroCommande]); // Remplacez 'numeroCommande' par le champ approprié dans votre entité Commande

		// Vérifier si la commande existe
		if (!$commande) {
			$this->addFlash('error', 'Commande non trouvée.');
			return $this->redirectToRoute('app_syma_business'); // Remplacez par une route existante
		}

		// Récupérer les lignes de commande associées à cette commande
		$lignesCommande = $lignesCommandeRepo->findBy(['commande' => $commande]);

		// Renvoyer le rendu de la vue avec les lignes de commande
		return $this->render('interfaces_users/lignes_commande/show.html.twig', [
			'lignesCommande' => $lignesCommande,
			'numeroCommande' => $numeroCommande,
			'commande' => $commande,
		]);
	}
}
