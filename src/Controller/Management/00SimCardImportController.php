<?php

namespace App\Controller\Management;

use App\Entity\PendingSimCards;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SimCardImportController extends AbstractController
{
	#[Route('/management/import-sim-cards', name: 'import_sim_cards', methods: ['GET', 'POST'])]
	public function import(Request $request, EntityManagerInterface $entityManager, SimTypeRepository $simTypeRepository): Response
	{
		$simTypes = $simTypeRepository->findAll();

		if ($request->isMethod('POST')) {
			$type = $request->request->get('type'); // Récupérer le type depuis le formulaire
			$simTypeRecived = $simTypeRepository->findBy(['code' => $type]);
			

			/** @var UploadedFile $csvFile */
			$csvFile = $request->files->get('csv_file');

			if ($csvFile && $csvFile->getClientOriginalExtension() === 'csv') {
				if (($handle = fopen($csvFile->getPathname(), 'r')) !== false) {
					fgetcsv($handle); // Ignorer la ligne d'en-tête

					$rowCount = 0;
					$existingSerialNumbers = [];

					while (($data = fgetcsv($handle, 1000, ',')) !== false) {
						if (isset($data[0])) {
							$serialNumber = trim($data[0]);

							if (!empty($serialNumber)) {
								// Vérifier si le numéro de série existe déjà
								$existingCard = $entityManager->getRepository(PendingSimCards::class)->findOneBy(['serialNumber' => $serialNumber]);

								if ($existingCard) {
									// Collecter les numéros de série existants pour les signaler
									$existingSerialNumbers[] = $serialNumber;
									continue; // Passer à l'entrée suivante
								}

								// Créer un nouvel objet PendingSimCards
								$pendingSimCard = new PendingSimCards();
								$pendingSimCard->setSerialNumber($serialNumber);
								$pendingSimCard->setType($simTypeRecived[0]); // Utiliser le type du formulaire
								$pendingSimCard->setImportedCsv(True);

								$entityManager->persist($pendingSimCard);
								$rowCount++;
							} else {
								$this->addFlash('error', "Données invalides dans le fichier CSV à la ligne {$rowCount}.");
							}
						}
					}
					fclose($handle);

					$entityManager->flush();

					// Afficher un message de succès pour les cartes SIM importées
					$this->addFlash('success', "{$rowCount} cartes SIM importées avec succès !");

					// Afficher les messages flash pour les numéros de série existants
					if (!empty($existingSerialNumbers)) {
						$existingList = implode(", ", $existingSerialNumbers);
						$this->addFlash('error', "Les numéros suivants existent déjà dans la base : {$existingList}");
					}
				} else {
					$this->addFlash('error', 'Impossible d\'ouvrir le fichier CSV.');
				}
			} else {
				$this->addFlash('error', 'Veuillez télécharger un fichier CSV valide.');
			}
		}

		return $this->render('interfaces_admin/sim_card_import/import.html.twig', [
			'simTypes' => $simTypes
		]);
	}
}
