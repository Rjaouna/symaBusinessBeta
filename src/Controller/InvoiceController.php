<?php

namespace App\Controller;

use App\Service\InvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class InvoiceController extends AbstractController
{
	#[Route('/generate-invoices-avv', name: 'generate_all_invoices_avv')]
	public function generateAllInvoices(InvoiceService $invoiceService): Response
	{
		// Vérifier si l'utilisateur est un administrateur
		if (!$this->isGranted('ROLE_ADMIN')) {
			return new Response('Accès refusé. Vous devez être un administrateur pour générer des factures.', Response::HTTP_FORBIDDEN);
		}

		// Appeler le service pour générer les factures pour tous les utilisateurs
		$invoiceService->generateInvoicesForAllUsers();

		$this->addFlash('success', 'Les factures et les avoirs ont été générés avec succès. Vous pouvez les consulter dans votre espace de gestion');

		return $this->redirectToRoute('app_syma_business');
	}
}
