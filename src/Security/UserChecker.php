<?php
// src/Security/UserChecker.php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
	public function checkPreAuth(UserInterface $user): void
	{
		// Vérifier que l'utilisateur est bien une instance de la classe User
		if (!$user instanceof \App\Entity\User) {
			return;
		}

		// Si l'utilisateur n'est pas vérifié, lancer une exception avec un message
		if (!$user->isVerified()) {
			throw new CustomUserMessageAuthenticationException('Votre compte n\'est pas encore activé.');
		}
	}

	public function checkPostAuth(UserInterface $user): void
	{
		// Aucune vérification post-authentification dans cet exemple
	}
}
