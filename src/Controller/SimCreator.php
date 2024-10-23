<?php

namespace App\Controller;

use App\Entity\CarteSim;
use App\Form\CarteSimBatchType;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/simcreator')]
final class SimCreator extends AbstractController
{
	private EntityManagerInterface $entityManager;
	private SimTypeRepository $simTypeRepository;

	public function __construct(EntityManagerInterface $entityManager, SimTypeRepository $simTypeRepository)
	{
		$this->entityManager = $entityManager;
		$this->simTypeRepository = $simTypeRepository;
	}

	#[Route('/create', name: 'app_carte_sim_batch')]
	public function createSimCards(Request $request): Response
	{
		$form = $this->createForm(CarteSimBatchType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			$firstSerialNumber = (string) $data['firstSerialNumber'];
			$lastSerialNumber = (string) $data['lastSerialNumber'];

			// Rechercher le type de carte SIM par son nom
			$typeCarteSim = $this->simTypeRepository->findOneBy(['nom' => $data['typeCarteSim']]);

			// Vérifiez si le type de carte SIM existe
			if (!$typeCarteSim) {
				$this->addFlash('error', "Le type de carte SIM spécifié n'existe pas.");
				return $this->redirectToRoute('app_carte_sim_index');
			}

			// Appel de la méthode pour créer les cartes SIM
			$result = $this->generateSimCards($firstSerialNumber, $lastSerialNumber, $typeCarteSim);

			// Ajouter un message de confirmation
			$this->addFlash('success', $result);
			return $this->redirectToRoute('app_carte_sim_index');
		}

		return $this->render('carte_sim/SimCreator.html.twig', [
			'form' => $form->createView(),
		]);
	}

	private function generateSimCards(string $firstSerialNumber, string $lastSerialNumber, $typeCarteSim): string
	{
		// Vérifier que les deux numéros ont la même longueur
		if (strlen($firstSerialNumber) !== strlen($lastSerialNumber)) {
			return "Les numéros de série doivent avoir la même longueur.";
		}

		$startDiffIndex = -1;
		$endDiffIndex = -1;

		// Parcourir les numéros pour trouver les indices de différence
		for ($i = 0; $i < strlen($firstSerialNumber); $i++) {
			if ($firstSerialNumber[$i] !== $lastSerialNumber[$i]) {
				if ($startDiffIndex === -1) {
					$startDiffIndex = $i;
				}
				$endDiffIndex = $i;
			}
		}

		// Si aucune différence n'est trouvée
		if ($startDiffIndex === -1) {
			return "Les numéros de série sont identiques.";
		}

		// Extraire la partie variable
		$beforeDiff = substr($firstSerialNumber, 0, $startDiffIndex);
		$afterDiff = substr($firstSerialNumber, $endDiffIndex + 1);

		// Partie changeante des numéros
		$variableFirst = substr($firstSerialNumber, $startDiffIndex, $endDiffIndex - $startDiffIndex + 1);
		$variableLast = substr($lastSerialNumber, $startDiffIndex, $endDiffIndex - $startDiffIndex + 1);

		// Calculer la différence entre les parties variables
		$totalVariableDiff = intval($variableLast) - intval($variableFirst) + 1;

		// Création des cartes SIM
		for ($i = intval($variableFirst); $i <= intval($variableLast); $i++) {
			// Générer le numéro de série actuel
			$currentSerialVariable = str_pad($i, strlen($variableFirst), '0', STR_PAD_LEFT);
			$currentSerial = $beforeDiff . $currentSerialVariable . $afterDiff;

			// Créer une nouvelle entité CarteSim
			$carteSim = new CarteSim();
			$carteSim->setSerialNumber($currentSerial);
			$carteSim->setType($typeCarteSim);
			$carteSim->setReserved(false);

			// Persister la carte dans la base de données
			$this->entityManager->persist($carteSim);
		}

		// Sauvegarder toutes les cartes dans la base de données
		$this->entityManager->flush();

		return "Création de $totalVariableDiff cartes SIM terminée avec succès.";
	}
}
