<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Form\ZoneType;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Service\CodeGenerator;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/zone')]
class ZoneController extends AbstractController
{
	#[Route('/', name: 'zone_index', methods: ['GET'])]
	public function index(ZoneRepository $zoneRepository): Response
	{
		return $this->render('zone/index.html.twig', [
			'zones' => $zoneRepository->findAll(),
		]);
	}

	#[Route('/new', name: 'zone_new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager, ZoneRepository $zoneRepository): Response
	{
		$zone = new Zone();
		$form = $this->createForm(ZoneType::class, $zone);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Générer un code unique
			$uniqueCode = $this->generateUniqueCode($zoneRepository);
			$zone->setCodeZone($uniqueCode);

			try {
				$entityManager->persist($zone);
				$entityManager->flush();

				$this->addFlash('success', 'La zone a été créée avec succès avec le code ' . $uniqueCode . '.');

				return $this->redirectToRoute('zone_index', [], Response::HTTP_SEE_OTHER);
			} catch (UniqueConstraintViolationException $e) {
				// Si le code est déjà utilisé, génère un nouveau code et réessaie
				$this->addFlash('danger', 'Erreur lors de la création de la zone. Veuillez réessayer.');

				// Optionnel : Log l'erreur pour le débogage
				// $this->logger->error('Erreur de contrainte unique: ' . $e->getMessage());

				return $this->redirectToRoute('zone_new');
			}
		}


		return $this->render('zone/new.html.twig', [
			'zone' => $zone,
			'form' => $form->createView(),
		]);
	}

	#[Route('/{id}/edit', name: 'zone_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Zone $zone, EntityManagerInterface $entityManager): Response
	{
		// Créer le formulaire en liant l'entité Zone existante
		$form = $this->createForm(ZoneType::class, $zone);
		$form->handleRequest($request);

		// Vérifier si le formulaire a été soumis et est valide
		if ($form->isSubmitted() && $form->isValid()) {
			// Persist les modifications apportées à l'entité Zone
			$entityManager->flush();

			// Ajouter un message flash de succès
			$this->addFlash('success', 'La zone a été mise à jour avec succès.');

			// Rediriger vers la page de liste des zones ou une autre page appropriée
			return $this->redirectToRoute('zone_index');
		}

		// Rendre le formulaire d'édition
		return $this->render('zone/edit.html.twig', [
			'zone' => $zone,
			'form' => $form->createView(),
		]);
	}
	private function generateUniqueCode(ZoneRepository $zoneRepository): string
	{
		do {
			// Génère un nombre aléatoire à 6 chiffres avec des zéros initiaux si nécessaire
			$randomNumber = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
			$code = 'Zone_code_' . $randomNumber;
			// Vérifie si le code existe déjà
			$existingZone = $zoneRepository->findOneBy(['codeZone' => $code]);
		} while ($existingZone !== null);

		return $code;
	}
}
