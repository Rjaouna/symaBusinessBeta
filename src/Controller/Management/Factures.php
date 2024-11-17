<?php

namespace App\Controller\Management;

use Doctrine\ORM\EntityManager;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class Factures extends AbstractController
{
	private $entityManager;
	private $factureRepository;

	public function __construct(FactureRepository $factureRepository, EntityManagerInterface $entityManager)
	{
		$this->factureRepository = $factureRepository;
		$this->entityManager = $entityManager;
	}

	#[Route('/factures', name: 'app_facture_list')]
	public function factureList(): Response
	{
		// Récupérer toutes les factures
		$factures = $this->factureRepository->findAll();

		// Passer les factures à la vue
		return $this->render('interfaces_admin/factures/index.html.twig', [
			'factures' => $factures
		]);
	}

	#[Route('/facture/{id}/update-paiement', name: 'facture_update_paiement')]
	public function factureUpdatePaiement(int $id, Request $request): Response
	{
		// Récupérer la facture à partir de l'ID
		$facture = $this->factureRepository->find($id);

		if (!$facture) {
			// Si la facture n'existe pas, rediriger ou afficher une erreur
			throw $this->createNotFoundException('Facture non trouvée.');
		}

		// Récupérer la valeur envoyée depuis le formulaire
		$modePaiement = $request->request->get('modePaiement');

		// Mettre à jour le statut de la facture et éventuellement d'autres champs
		$facture->setStatutPaiement('Payé');
		$facture->setModePaiement($modePaiement);

		// Sauvegarder la facture
		$this->entityManager->flush();

		// Rediriger vers la liste des factures avec un message flash
		$this->addFlash('success', 'Le paiement a été validé avec succès.');

		return $this->redirectToRoute('app_facture_list');
	}

	
	#[Route('factures/client', name: 'app_facture_client')]
	public function factureClient(): Response
	{
		// Récupérer l'utilisateur connecté
		$user = $this->getUser();

		// Vérifier si l'utilisateur est bien connecté
		if (!$user) {
			// Rediriger vers une page de connexion si l'utilisateur n'est pas connecté
			return $this->redirectToRoute('app_login');
		}

		// Récupérer toutes les factures pour l'utilisateur connecté
		$factures = $this->factureRepository->findBy(['client' => $user]);

		// Passer les factures à la vue
		return $this->render('interfaces_users/factures/index.html.twig', [
			'factures' => $factures
		]);
	}

	#[Route('/facture/client/{id}', name: 'facture_client_detail')]
	public function factureClientDetail(int $id): Response
	{
		// Récupérer une facture spécifique et ses lignes
		$facture = $this->factureRepository->find($id);

		// Vérifier si la facture existe
		if (!$facture) {
			throw $this->createNotFoundException('Facture non trouvée');
		}
		// Marquer la facture comme vue
		$facture->setSeen(true);

		// Sauvegarder le changement dans la base de données
		$this->entityManager->persist($facture);
		$this->entityManager->flush();

		// Passer la facture et ses lignes à la vue
		return $this->render('interfaces_users/factures/show.html.twig', [
			'facture' => $facture
		]);
	}

	#[Route('/facture/{id}', name: 'facture_detail')]
	public function factureDetail(int $id): Response
	{
		// Récupérer une facture spécifique et ses lignes
		$facture = $this->factureRepository->find($id);

		// Vérifier si la facture existe
		if (!$facture) {
			throw $this->createNotFoundException('Facture non trouvée');
		}
		

		// Passer la facture et ses lignes à la vue
		return $this->render('interfaces_admin/factures/show.html.twig', [
			'facture' => $facture
		]);
	}

	
}
