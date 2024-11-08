<?php
// src/Controller/Management/ResetUserPropertiesController.php
namespace App\Controller\Management;

use App\Command\ResetUserPropertiesCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ResetUserPropertiesController extends AbstractController
{
	private ResetUserPropertiesCommand $resetUserPropertiesCommand;

	// Injection de la commande dans le constructeur
	public function __construct(ResetUserPropertiesCommand $resetUserPropertiesCommand)
	{
		$this->resetUserPropertiesCommand = $resetUserPropertiesCommand;
	}

	#[Route('/management/reset-user-properties', name: 'reset_user_properties', methods: ['POST'])]
	public function resetUserProperties(Request $request): Response
	{
		// Le mot de passe ou mot-clé que vous voulez vérifier
		$secretWord = 'password';

		// Récupère le mot de passe saisi dans le formulaire
		$passwordEntered = $request->request->get('password');

		// Compare le mot saisi avec le mot prédéfini
		if ($passwordEntered === $secretWord) {
			// Crée une instance de BufferedOutput pour capturer la sortie
			$output = new BufferedOutput();

			// Crée un tableau d'arguments pour la commande
			$input = new ArrayInput([]);

			// Exécute la commande
			$statusCode = $this->resetUserPropertiesCommand->run($input, $output);

			// Récupère le contenu de la sortie
			$outputContent = $output->fetch();

			// Vérifie si la commande s'est bien exécutée
			if ($statusCode === 0) {
				$this->addFlash('success', 'La réinitialisation des usages mensuels a été effectuée avec succès.');
			} else {
				$this->addFlash('error', 'Une erreur est survenue lors de la réinitialisation des usages mensuels.');
			}

			// Redirige vers la page appropriée après l'exécution de la commande
			return $this->redirectToRoute('app_syma_business');  // Remplacez par votre route cible
		}

		// Si le mot de passe est incorrect, afficher un message d'erreur
		$this->addFlash('error', 'Le mot de passe est incorrect.');

		// Redirige vers la page précédente
		return $this->redirectToRoute('app_syma_business');  // Remplacez par votre route cible
	}
}
