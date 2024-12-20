<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
	// Définir les clés et les valeurs de session comme constantes pour éviter les erreurs typographiques
	private const SESSION_KEY = 'authentification_commercial';
	private const SESSION_VALUE = 'votre_valeur_de_session'; // Remplacez par la valeur souhaitée
	private const SESSION_LIFETIME = 31536000; // Durée de vie de la session en secondes (ex: 1 heure)


	#[Route('/add-session', name: 'app_add_session', methods: ['POST', 'GET'])]
	public function addSession(SessionInterface $session): Response
	{

		// Stocker la valeur dans la session
		$session->set(self::SESSION_KEY, self::SESSION_VALUE);
		// Créer une réponse de redirection
		return $this->redirectToRoute('app_login');
	}

	/**
	 * Route pour supprimer une donnée de la session
	 */
	#[Route('/remove-session', name: 'app_remove_session', methods: ['POST', 'GET'])]
	public function removeSession(SessionInterface $session): Response
	{



		// Vérifier si la clé existe dans la session
		if ($session->has(self::SESSION_KEY)) {
			// Supprimer la valeur de la session
			$session->remove(self::SESSION_KEY);

			$this->addFlash(
				'success',
				"La session '" . self::SESSION_KEY . "' a été supprimée."
			);
		} else {
			// Ajouter un flash message d'erreur
			$this->addFlash(
				'danger',
				"La session '" . self::SESSION_KEY . "' n'existe pas."
			);
		}

		// Vous pouvez également invalider complètement la session si nécessaire
		// $session->invalidate();

		return $this->redirect('https://symamobile.fr');
	}
}
