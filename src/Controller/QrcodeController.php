<?php
// src/Controller/QRCodeController.php

namespace App\Controller;

use App\Service\QRCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/qrcode')]
class QrcodeController extends AbstractController
{
	private QRCodeService $qrCodeService;

	public function __construct(QRCodeService $qrCodeService)
	{
		$this->qrCodeService = $qrCodeService;
	}

	#[Route('/generate-qrcode/{id}', name: 'app_generate_qrcode', methods: ['GET'])]
	public function generateQRCode(int $id): Response
	{
		// Générer le QR code via le service
		$qrCodeImage = $this->qrCodeService->generateUserQRCode($id);

		// Créer une réponse avec les headers appropriés pour le téléchargement
		return new Response($qrCodeImage, 200, [
			'Content-Type' => 'image/png',
			'Content-Disposition' => 'attachment; filename="qrcode-user-' . $id . '.png"',
		]);
	}
}
