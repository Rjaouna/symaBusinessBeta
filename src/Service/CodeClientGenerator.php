<?php
// src/Service/CodeClientGenerator.php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class CodeClientGenerator
{
	private UserRepository $userRepository;
	private EntityManagerInterface $entityManager;

	public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
	{
		$this->userRepository = $userRepository;
		$this->entityManager = $entityManager;
	}

	/**
	 * Génère un code client unique de la forme 'C' suivi de 8 chiffres.
	 *
	 * @return string
	 */
	public function generateUniqueCodeClient(): string
	{
		do {
			$codeClient = 'C' . str_pad((string)random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
			$existingUser = $this->userRepository->findOneBy(['codeClient' => $codeClient]);
		} while ($existingUser !== null);

		return $codeClient;
	}
}
