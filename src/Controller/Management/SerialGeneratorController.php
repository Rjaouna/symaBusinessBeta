<?php

namespace App\Controller\Management;

use App\Entity\CarteSim;
use App\Form\SerialNumberFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class SerialGeneratorController extends AbstractController
{
	#[Route('/management/generate-serial-numbers', name: 'generate_serial_numbers')]
	public function generateSerialNumbers(Request $request, EntityManagerInterface $entityManager): Response
	{
		$form = $this->createForm(SerialNumberFormType::class);
		$form->handleRequest($request);
		$serialNumbers = [];
		$importedSerialNumbers = []; // Numéros importés avec succès
		$notImportedSerialNumbers = []; // Numéros qui existent déjà
		$count = 0; // Variable pour compter les numéros de série importés

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			$prefix = $data['prefix'];
			$start = $data['start'];
			$end = $data['end'];
			$suffix = $data['suffix'];
			$simType = $data['simType']; // Récupération du type sélectionné

			$serialNumbers = $this->generateSerialNumbersList($prefix, $start, $end, $suffix);

			foreach ($serialNumbers as $serialNumber) {
				// Vérifier si le numéro de série existe déjà dans la base de données
				$existingCarteSim = $entityManager->getRepository(CarteSim::class)->findOneBy(['serialNumber' => $serialNumber]);
				if ($existingCarteSim !== null) { // Si le numéro de série existe
					$notImportedSerialNumbers[] = $serialNumber; // Ajouter à la liste des numéros non importés
					$this->addFlash('primary', "Carte SIM existe : $serialNumber</br>");
				} else {
					// Créer une nouvelle carte SIM seulement si elle n'existe pas
					$carteSim = new CarteSim();
					$carteSim->setSerialNumber($serialNumber);
					$carteSim->setType($simType); // Définition du type de carte SIM
					$carteSim->setReserved(false);
					$entityManager->persist($carteSim);
					$count++;
					$importedSerialNumbers[] = $serialNumber; // Ajouter à la liste des numéros importés
				}
			}

			$entityManager->flush();
		}

		return $this->render('serial_generator/index.html.twig', [
			'form' => $form->createView(),
			'serialNumbers' => $serialNumbers,
			'importedSerialNumbers' => $importedSerialNumbers, // Passer les numéros importés
			'notImportedSerialNumbers' => $notImportedSerialNumbers, // Passer les numéros non importés
			'count' => $count, // Passez le compteur à la vue
		]);
	}

	private function generateSerialNumbersList($prefix, $start, $end, $suffix): array
	{
		$serialNumbers = [];

		for ($i = $start; $i <= $end; $i++) {
			$middle = str_pad($i, strlen($start), '0', STR_PAD_LEFT);
			$serialNumbers[] = $prefix . $middle . $suffix;
		}

		return $serialNumbers;
	}
}
