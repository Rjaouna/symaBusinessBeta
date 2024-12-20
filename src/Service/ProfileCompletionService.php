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
		return $missingProperties;
	}
}
