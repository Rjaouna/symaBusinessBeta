<?php

namespace App\Controller\Management;

use App\Entity\PendingSimCards;
use App\Entity\CarteSim;
use App\Repository\PendingSimCardsRepository;
use App\Repository\SimTypeRepository;
use App\Repository\CarteSimRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SimCardMigrationController extends AbstractController
{
	#[Route('/migrate-sim-cards', name: 'migrate_sim_cards', methods: ['GET'])]
	public function migrate(
		EntityManagerInterface $entityManager,
		PendingSimCardsRepository $pendingCardRepository,
		SimTypeRepository $simTypeRepository,
		CarteSimRepository $carteSimRepository
	): Response {
		// Step 1: Retrieve all PendingSimCards with migrated = false
		$pendingCards = $pendingCardRepository->findBy(['migrated' => false]);
		$processedCount = 0;

		// Step 2: Iterate through each pending card and create a new CarteSim
		foreach ($pendingCards as $pendingCard) {
			// Check if a CarteSim with the same serial number already exists
			$serialVerification = $carteSimRepository->findOneBy(['serialNumber' => $pendingCard->getSerialNumber()]);
			if (!$serialVerification) {
				// Retrieve the SIM type based on the code
				$simType = $simTypeRepository->findOneBy(['code' => $pendingCard->getType()]);

				// Create a new CarteSim entity
				$carteSim = new CarteSim();
				$carteSim->setSerialNumber($pendingCard->getSerialNumber());
				$carteSim->setType($pendingCard->getType());
				$carteSim->setReserved(False);

				

				// Persist the new CarteSim entity
				$entityManager->persist($carteSim);
				$processedCount++;

				// Mark the pending card as migrated
				$pendingCard->setMigrated(true);
			} else {
				// If the serial number already exists, mark the pending card as migrated
				$pendingCard->setMigrated(true);
			}
		}

		// Step 3: Flush the changes to the database
		$entityManager->flush();
		$this->addFlash('success', sprintf('La synchronisation a été effectuée avec succès. %d carte(s) traitée(s).', $processedCount));
		// Redirect to the previous page (or a specific route)
		return $this->redirectToRoute('app_carte_sim_index'); // Replace 'your_route_name_here' with the actual route name
	}
}
