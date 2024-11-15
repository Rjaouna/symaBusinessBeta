<?php

namespace App\Controller;

use App\Repository\FactureRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;


class FactureToPdfController extends AbstractController
{
	#[Route('/invoice/pdf/{id}/processor', name: 'app_invoice_pdf_processor')]
	public function downloadFacturePdf($id, FactureRepository $fac): Response
	{
		// Configure DOMPDF
		$pdfOptions = new Options();
		$pdfOptions->set('defaultFont', 'Arial');

		$dompdf = new Dompdf($pdfOptions);

		// Récupérer la facture depuis la base de données
		$facture = $fac->find($id);

		if (!$facture) {
			throw $this->createNotFoundException('Facture introuvable');
		}

		// Générer le contenu HTML à partir de Twig
		// Charger le HTML dans DOMPDF
		$html = $this->renderView('interfaces_admin/factures/show.html.twig', [
			'facture' => $facture,
		]);



		// Charger le HTML dans DOMPDF
		$dompdf->loadHtml($html);

		// (Optionnel) Définir le format et l'orientation de la page
		$dompdf->setPaper('A4', 'portrait');

		// Générer le PDF
		$dompdf->render();

		// Télécharger le PDF
		return new Response($dompdf->output(), 200, [
			'Content-Type' => 'application/pdf',
			'Content-Disposition' => 'attachment; filename="facture_' . $facture->getNumeroFacture() . '.pdf"'
		]);
	}
}
