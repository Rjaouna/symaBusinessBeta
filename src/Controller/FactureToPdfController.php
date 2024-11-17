<?php

namespace App\Controller;

use App\Repository\FactureRepository;
use App\Repository\SymaBusinessConfigRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactureToPdfController extends AbstractController
{
	#[Route('/invoice/pdf/{id}/processor', name: 'app_invoice_pdf_processor')]
	public function downloadFacturePdf(int $id, FactureRepository $factureRepository, SymaBusinessConfigRepository $symaBusinessConfigRepository): Response
	{
		$agent = $symaBusinessConfigRepository->find(1);
		// Configurer les options de Dompdf
		$pdfOptions = new Options();
		$pdfOptions->set('defaultFont', 'Arial');
		$pdfOptions->set('isRemoteEnabled', true); // Permet de charger des images externes si nécessaire

		$dompdf = new Dompdf($pdfOptions);

		// Récupérer la facture depuis la base de données
		$facture = $factureRepository->find($id);

		if (!$facture) {
			throw $this->createNotFoundException('Facture introuvable.');
		}

		// Générer le contenu HTML à partir du template Twig
		$html = $this->renderView('interfaces_users/factures/etats/factures_etat.html.twig', [
			'facture' => $facture,
			'agent' => $agent,
			'bootstrapCss' => file_get_contents('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'), // Chemin local de Bootstrap
		]);


		// Charger le HTML dans Dompdf
		$dompdf->loadHtml($html);

		// Définir le format et l'orientation de la page
		$dompdf->setPaper('A4', 'portrait');

		// Générer le PDF
		$dompdf->render();

		// Télécharger le PDF
		$fileName = sprintf('facture_%s.pdf', $facture->getNumeroFacture());

		return new Response(
			$dompdf->output(),
			Response::HTTP_OK,
			[
			'Content-Type' => 'application/pdf',
				'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
		]);
	}
}
