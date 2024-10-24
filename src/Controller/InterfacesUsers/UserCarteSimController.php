<?php
// src/Controller/CarteSimController.php
namespace App\Controller\InterfacesUsers;

use App\Entity\CarteSim;
use App\Repository\CarteSimRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserCarteSimController extends AbstractController
{
	#[Route('/cartesim/purchased', name: 'cartesim_purchased')]
	public function listPurchasedCards(CarteSimRepository $carteSimRepo): Response
	{
		// Récupérer l'utilisateur connecté
		$user = $this->getUser();

		// Vérifier si l'utilisateur est connecté
		if (!$user) {
			return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
		}

		// Récupérer les cartes SIM achetées par l'utilisateur
		$cartesSimPurchased = $carteSimRepo->findBy(['purchasedBy' => $user]);

		// Renvoyer la réponse avec la liste des cartes SIM
		return $this->render('interfaces_users/carte_sim/list.html.twig', [
			'cartesSim' => $cartesSimPurchased,
		]);
	}
}
