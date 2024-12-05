<?php
// src/Service/QRCodeService.php

namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Color\Color;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class QRCodeService
{
	private UrlGeneratorInterface $urlGenerator;

	public function __construct(UrlGeneratorInterface $urlGenerator)
	{
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * Génère un QR code pour un utilisateur donné.
	 *
	 * @param int $userId ID de l'utilisateur
	 * @return string Contenu binaire de l'image PNG du QR code
	 */
	public function generateUserQRCode(int $userId): string
	{
		// Construire l'URL vers le profil de l'utilisateur
		$url = $this->urlGenerator->generate('advinced_biper_commande_comptoir', ['clientId' => $userId], UrlGeneratorInterface::ABSOLUTE_URL);

		// Générer le QR code
		$result = Builder::create()
			->writer(new PngWriter())
			->data($url)
			->encoding(new Encoding('UTF-8'))
			->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
			->size(300) // Taille en pixels
			->margin(10) // Marge en pixels
			->roundBlockSizeMode(new RoundBlockSizeModeMargin())
			->backgroundColor(new Color(255, 255, 255)) // Couleur de fond blanche
			->foregroundColor(new Color(0, 0, 0)) // Couleur du QR code noire
			->build();

		// Récupérer l'image en tant que données binaires
		return $result->getString();
	}
}
