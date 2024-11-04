<?php
// src/Service/SerialNumberComparator.php

namespace App\Service;

use App\Entity\CarteSim;
use App\Entity\PendingSimCards;
use Doctrine\ORM\EntityManagerInterface;

class SerialNumberComparator
{
	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function compareSerialNumbers(): bool
	{
		// Fetch all serial numbers from the CarteSim entity
		$carteSims = $this->entityManager->getRepository(CarteSim::class)->findAll();
		$carteSimSerialNumbers = array_map(fn($carteSim) => $carteSim->getSerialNumber(), $carteSims);

		// Fetch all serial numbers from the PendingSimCards entity
		$pendingSims = $this->entityManager->getRepository(PendingSimCards::class)->findAll();
		$pendingSimSerialNumbers = array_map(fn($pendingSim) => $pendingSim->getSerialNumber(), $pendingSims);

		// Compare and find missing serial numbers
		$missingSerialNumbers = array_diff($pendingSimSerialNumbers, $carteSimSerialNumbers);

		// Return true if there are missing serial numbers, otherwise false
		return !empty($missingSerialNumbers);
	}
}
