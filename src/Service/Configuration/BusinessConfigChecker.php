<?php

namespace App\Service\Configuration;

use App\Entity\SymaBusinessConfig;
use Doctrine\ORM\EntityManagerInterface;

class BusinessConfigChecker
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function isConfigComplete(): bool
	{
		// Récupérer la configuration de la société
		$config = $this->entityManager->getRepository(SymaBusinessConfig::class)->findOneBy([]);

		// Vérifier si une configuration existe
		if (!$config) {
			return false; // Pas de configuration
		}

		// Vérifier si toutes les propriétés essentielles sont remplies
		return !empty($config->getNomDuResponsable()) &&
			!empty($config->getEmail()) &&
			!empty($config->getNumeroDeTelephone()) &&
			!empty($config->getRaisonSociale()) &&
			!empty($config->getNumeroDeRegistre()) &&
			!empty($config->getFormeJuridique()) &&
			!empty($config->getCodeApeNaf()) &&
			!empty($config->getCapitalSocial()) &&
			!empty($config->getNumeroSiret());
	}
}
