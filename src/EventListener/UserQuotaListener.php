<?php

namespace App\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\User;
use App\Entity\Quota; // Assurez-vous que vous importez la classe Quota
use App\Repository\QuotaRepository;
use App\Service\EmailService; // Assurez-vous que le service est bien importé
use DateTime; // Importez la classe DateTime si nécessaire
use Symfony\Component\Routing\RouterInterface; // Importer la bonne interface
use Doctrine\ORM\EntityManagerInterface; // Importer l'interface EntityManager

class UserQuotaListener
{
	private EmailService $emailService;
	private RouterInterface $router;
	private EntityManagerInterface $entityManager;
	private QuotaRepository $quotaRepository;

	private array $quotaOrder = [
		'first' => 1,
		'second' => 2,
		'third' => 3,
		'fourth' => 4,
	];

	public function __construct(EmailService $emailService, RouterInterface $router, EntityManagerInterface $entityManager, QuotaRepository $quotaRepository)
	{
		$this->emailService = $emailService;
		$this->router = $router;
		$this->entityManager = $entityManager; // Initialize EntityManager
		$this->quotaRepository = $quotaRepository; // Initialize QuotaRepository
	}

	public function preUpdate(PreUpdateEventArgs $args): void
	{
		$entity = $args->getObject();

		if (!$entity instanceof User) {
			return;
		}

		// Check if lastUpdatedQuotaAt is empty
		if (empty($entity->getLastUpdatedQuotaAt())) {
			$quota = $this->quotaRepository->find(1); // Use the repository to find the quota

			if ($quota) { // Ensure the quota exists before accessing its methods
				// Update lastUpdatedQuotaAt to the current date and time
				$entity->setLastUpdatedQuotaAt(new \DateTimeImmutable());

				// Create the context for the assigned quota email
				$context = [
					'user' => $entity,
					'quota' => $quota->getNom(), // Retrieve the assigned quota name
					'lastUpdatedQuotaAt' => $entity->getLastUpdatedQuotaAt(), // Use the updated value
					'clientDashboardUrl' => $this->router->generate('app_user_Bonus_Controller'),
				];

				// Send the email with the "assigned quota" template
				$this->emailService->sendQuotaChangeEmail($entity, 'emails/quotas/quota_assigned.html.twig', $context);

				// Persist the user entity with the updated lastUpdatedQuotaAt
				$this->entityManager->persist($entity);
				$this->entityManager->flush(); // Save changes to the database
			}

			return; // No further actions if lastUpdatedQuotaAt is empty
		}

		if ($args->hasChangedField('quotas')) {
			/** @var Quota $oldQuota */
			$oldQuota = $args->getOldValue('quotas');
			/** @var Quota $newQuota */
			$newQuota = $args->getNewValue('quotas');

			// Get the codes for comparison
			$oldCode = $oldQuota->getCode(); // Ensure you have this method
			$newCode = $newQuota->getCode();

			// Compare the codes to determine the change
			$template = $this->determineTemplate($oldCode, $newCode);

			// Create context for the email template
			$context = [
				'user' => $entity,
				'oldQuota' => $oldQuota,
				'newQuota' => $newQuota,
				'updateDate' => new DateTime(),
				'clientDashboardUrl' => $this->router->generate('app_user_Bonus_Controller'),
			];

			// Send email with the selected template
			$this->emailService->sendQuotaChangeEmail($entity, $template, $context);
		}
	}

	private function determineTemplate(string $oldCode, string $newCode): string
	{
		// Compare the order based on the defined quota order
		$oldOrder = $this->quotaOrder[$oldCode] ?? null;
		$newOrder = $this->quotaOrder[$newCode] ?? null;

		if ($oldOrder === null || $newOrder === null) {
			// Handle the case where an invalid code is given (optional)
			return 'emails/quotas/error_template.html.twig'; // Change as necessary
		}

		return $newOrder > $oldOrder
			? 'emails/quotas/quota_increased.html.twig'
			: 'emails/quotas/quota_reduced.html.twig';
	}
}
