<?php

namespace App\Controller\InterfacesUsers;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\SimUsageUpdater;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/user/commande')]
final class UserCommandeController extends AbstractController
{
	#[Route(name: 'user_app_commande_index', methods: ['GET'])]
	public function index(CommandeRepository $commandeRepository): Response
	{
		return $this->render('interfaces_users/commande/index.html.twig', [
			'commandes' => $commandeRepository->findBy(['user' => $this->getUser()]),
		]);
	}

	#[Route('/new', name: 'user_app_commande_new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager, CommandeRepository $commandeRepository, SimTypeRepository $simTypeRepository, SimUsageUpdater $simUsageUpdater): Response
	{
		$user = $this->getUser();
		if (!$user) {
			// Si l'utilisateur n'est pas authentifié, renvoie une erreur ou redirige
			throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette fonctionnalité.');
		}
		$simTypeCode = $request->request->get('simType');  // Récupère le code sélectionné dans le formulaire
		$quantity = $request->request->get('quantity');    // Récupère la quantité sélectionnée dans le formulaire
		$codeClient = $user->getCodeClient();

		if (!$codeClient) {
			$this->addFlash('danger', 'Votre compte n\'est pas encore activé.');  // Message flash d'erreur
			return $this->redirectToRoute('app_syma_business');  // Rediriger vers une route appropriée
		}

		// Récupérer l'objet SimType correspondant à partir du code
		$typeSim = $simTypeRepository->findOneBy(['code' => $simTypeCode]);

		if (!$typeSim) {
			$this->addFlash('danger', 'Aucun type de SIM trouvé.');  // Message flash d'erreur
			return $this->redirectToRoute('app_syma_business');  // Rediriger vers une route appropriée
		}

		$prix = $typeSim->getPrix();  // Récupérer le prix du type de SIM

		// Générer un nouveau numéro de commande
		$lastCommande = $commandeRepository->findOneBy([], ['id' => 'DESC']);
		$newCode = $lastCommande ? 'CDE_' . str_pad((int)substr($lastCommande->getNumero(), 4) + 1, 6, '0', STR_PAD_LEFT) : 'CDE_000001';
		//Vérifier combien de bonus utiliser 

		// Créer et persister la nouvelle commande
		$commande = new Commande();
		$commande->setNumero($newCode);
		$commande->setUser($user);
		$commande->setQte($quantity * 5);
		$commande->setTotal(($quantity * 5) * $prix);
		$commande->setStatus('en_attente');
		$commande->setSimType($typeSim->getNom());
		$commande->setTypeSim($typeSim);
		$commande->setCodeClient($codeClient);

		$entityManager->persist($commande);
		$entityManager->flush();
		// Utilise le service pour mettre à jour l'usage
		try {
			$simUsageUpdater->updateSimUsage($user, $typeSim->getcode(), $quantity);
			$this->addFlash('light', 'L\'usage du type de SIM a été mis à jour avec succès.');
		} catch (\InvalidArgumentException $e) {
			$this->addFlash('danger', $e->getMessage());
		}

		$this->addFlash('light', 'Commande créée avec succès ! ');  // Message flash de succès

		return $this->redirectToRoute('app_syma_business');
	}



	// #[Route('/{id}', name: 'user_app_commande_show', methods: ['GET'])]
	// public function show(Commande $commande): Response
	// {
	// 	return $this->render('commande/show.html.twig', [
	// 		'commande' => $commande,
	// 	]);
	// }

}
