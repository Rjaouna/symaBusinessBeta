<?php

namespace App\Service;

use App\Entity\PendingSimCards;
use App\Entity\Chapelet;
use Doctrine\ORM\EntityManagerInterface;

class CarteSimImportService
{
	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * Importation des cartes SIM depuis un fichier CSV et attribution d'un type de carte SIM.
	 *
	 * @param string $filePath Le chemin du fichier CSV
	 * @param mixed $simType Le type de carte SIM (peut être un objet ou une valeur)
	 * @return array Liste des erreurs, s'il y en a
	 */
	public function importFromCsvWithSimType(string $filePath, $simType): array
	{
		$errors = [];

		// Ouvrir le fichier CSV en mode lecture
		$handle = fopen($filePath, 'r');

		if ($handle === false) {
			return ['Impossible d’ouvrir le fichier CSV.'];
		}

		// Lire toutes les lignes du fichier CSV dans un tableau
		$rows = [];
		while (($data = fgetcsv($handle, 1000, ',')) !== false) {
			$rows[] = $data;
		}



		// Validation de chaque ligne et vérification des erreurs
		foreach ($rows as $lineNumber => $data) {
			[$serialNumber, $codeChapelet] = $data;

			// Validation de la longueur du serialNumber et du codeChapelet
			if (strlen($serialNumber) !== 19) {
				$errors[] = "Erreur à la ligne " . ($lineNumber + 1) . ": Le numéro de série $serialNumber doit avoir 19 caractères.";
				break; // Sortir de la boucle dès qu'une erreur est trouvée
			}

			if (strlen($codeChapelet) !== 14) {
				$errors[] = "Erreur à la ligne " . ($lineNumber + 1) . ": Le code du chapelet $codeChapelet doit avoir 14 caractères.";
				break; // Sortir de la boucle dès qu'une erreur est trouvée
			}

			// Vérifier si un chapelet avec ce code existe déjà
			$chapelet = $this->entityManager->getRepository(Chapelet::class)
				->findOneBy(['codeChapelet' => $codeChapelet]);

			if (!$chapelet) {
				// Si le chapelet n'existe pas, créer un nouveau chapelet
				$chapelet = new Chapelet();
				$chapelet->setCodeChapelet($codeChapelet);
				$chapelet->setReserved(false);
				$chapelet->setTypeCartes($simType);
				$this->entityManager->persist($chapelet);
				$this->entityManager->flush(); // Persister le chapelet immédiatement pour garantir qu'il existe dans la base
			}

			// Vérifier si la carte SIM existe déjà
			$existingSim = $this->entityManager->getRepository(PendingSimCards::class)
				->findOneBy(['serialNumber' => $serialNumber]);

			if (!$existingSim) {
				// Si la carte SIM n'existe pas, créer une nouvelle carte SIM
				$pendingSimCards = new PendingSimCards();
				$pendingSimCards->setSerialNumber($serialNumber);
				$pendingSimCards->setType($simType);
				$pendingSimCards->setChapelet($chapelet);
				$pendingSimCards->setImportedCsv(true);
				$this->entityManager->persist($pendingSimCards);
			} else {
				// Ajouter un message d'erreur si la carte SIM existe déjà
				$errors[] = "Le numéro $serialNumber existe déjà. Veuillez vérifier et essayer avec un autre numéro.<br>";
			}
		}

		fclose($handle);

		// Persister toutes les entités (chapelets et cartes SIM) dans la base de données
		$this->entityManager->flush();

		// Retourner la liste des erreurs
		return $errors;
	}
}
