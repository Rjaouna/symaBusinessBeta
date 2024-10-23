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

#[Route('/user/commande')]
final class UserCommandeController extends AbstractController
{
	#[Route(name: 'user_app_commande_index', methods: ['GET'])]
	public function index(CommandeRepository $commandeRepository): Response
	{
		return $this->render('interfaces_users/commande/index.html.twig', [
			'commandes' => $commandeRepository->findAll(),
		]);
	}

	#[Route('/new', name: 'user_app_commande_new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager, CommandeRepository $commandeRepository, SimTypeRepository $simTypeRepository): Response
	{
		$user = $this->getUser();
		$simTypeCode = $request->request->get('simType');  // Récupère le code sélectionné dans le formulaire
		$quantity = $request->request->get('quantity');    // Récupère la quantité sélectionnée dans le formulaire
		$codeClient = $user->getCodeClient();

		if (!$codeClient) {
			$this->addFlash('error', 'Votre compte n\'est pas encore activé.');  // Message flash d'erreur
			return $this->redirectToRoute('some_route');  // Rediriger vers une route appropriée
		}

		// Récupérer l'objet SimType correspondant à partir du code
		$typeSim = $simTypeRepository->findOneBy(['code' => $simTypeCode]);

		if (!$typeSim) {
			$this->addFlash('error', 'Aucun type de SIM trouvé.');  // Message flash d'erreur
			return $this->redirectToRoute('app_syma_business');  // Rediriger vers une route appropriée
		}

		$prix = $typeSim->getPrix();  // Récupérer le prix du type de SIM

		// Générer un nouveau numéro de commande
		$lastCommande = $commandeRepository->findOneBy([], ['id' => 'DESC']);
		$newCode = $lastCommande ? 'CDE_' . str_pad((int)substr($lastCommande->getNumero(), 4) + 1, 6, '0', STR_PAD_LEFT) : 'CDE_00001';

		// Créer et persister la nouvelle commande
		$commande = new Commande();
		$commande->setNumero($newCode);
		$commande->setUser($user);
		$commande->setQte($quantity);
		$commande->setTotal($quantity * $prix);
		$commande->setStatus('en_attente');
		$commande->setSimType($typeSim->getNom());
		$commande->setCodeClient($codeClient);

		$entityManager->persist($commande);
		$entityManager->flush();

		$this->addFlash('success', 'Commande créée avec succès ! ');  // Message flash de succès

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
