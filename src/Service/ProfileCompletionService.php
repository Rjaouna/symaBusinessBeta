<?php
// src/Service/ProfileCompletionService.php

namespace App\Service;

use App\Entity\User;

class ProfileCompletionService
{
	public function checkProfileCompleteness(User $user): array
	{
		$missingProperties = [];

		// Vérification des propriétés manquantes
		if (empty($user->getNomSociete())) {
			$missingProperties[] = 'nomSociete';
		}
		if (empty($user->getFormeJuridique())) {
			$missingProperties[] = 'formeJuridique';
		}
		if (empty($user->getNumeroRegistreCommerce())) {
			$missingProperties[] = 'numeroRegistreCommerce';
		}
		if (empty($user->getNumeroSiret())) {
			$missingProperties[] = 'numeroSiret';
		}
		if (empty($user->getNumeroRCS())) {
			$missingProperties[] = 'numeroRCS';
		}
		if (empty($user->getCodeAPE())) {
			$missingProperties[] = 'codeAPE';
		}
		if (empty($user->getKbis())) {
			$missingProperties[] = 'kbis';
		}
		if (empty($user->getAdresse())) {
			$missingProperties[] = 'adresse';
		}
		if (empty($user->getPays())) {
			$missingProperties[] = 'pays';
		}
		if (empty($user->getCodePostal())) {
			$missingProperties[] = 'codePostal';
		}
		if (empty($user->getVille())) {
			$missingProperties[] = 'ville';
		}
		if (empty($user->getIban())) {
			$missingProperties[] = 'iban';
		}
		if (empty($user->getBic())) {
			$missingProperties[] = 'bic';
		}

		return $missingProperties;
	}
}