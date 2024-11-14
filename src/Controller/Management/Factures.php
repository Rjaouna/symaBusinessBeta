<?php

namespace App\Controller\Management;

use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Factures extends AbstractController
{
	private $factureRepository;

	public function __construct(FactureRepository $factureRepository)
	{
		$this->factureRepository = $factureRepository;
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
