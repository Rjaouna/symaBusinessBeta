<?php

namespace App\Controller\Management;

use App\Form\CarteSimImportType;
use App\Service\CarteSimImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/management/import')]
class ImportCarteSimController extends AbstractController
{
	#[Route('/carte-sim', name: 'management_import_carte_sim', methods: ['GET', 'POST'])]
	public function import(
		Request $request,
		EntityManagerInterface $entityManager,
		CarteSimImportService $importService // Injecter le service ici
	): Response {
		$form = $this->createForm(CarteSimImportType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$csvFile = $form->get('csvFile')->getData();
			$simType = $form->get('simType')->getData(); // Récupère le type de carte SIM

			if ($csvFile) {
				$filePath = $csvFile->getRealPath();

				// Utiliser le service injecté
				$errors = $importService->importFromCsvWithSimType($filePath, $simType);

				if (empty($errors)) {
					$this->addFlash('success', 'Importation réussie !');
				} else {
					foreach ($errors as $error) {
						$this->addFlash('error', $error);
					}
				}
			}
		}

		return $this->render('interfaces_admin/import/import.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
