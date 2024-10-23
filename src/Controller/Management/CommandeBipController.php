<?php
// src/Controller/BiperController.php
namespace App\Controller\Management;

use App\Entity\CarteSim;
use App\Entity\Commande;
use App\Entity\LignesCommande;
use App\Form\CommandeValidationType;
use App\Repository\CarteSimRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LignesCommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeBipController extends AbstractController
{
	#[Route('/biper/{clientId}/serial-number', name: 'biper_serial_number')]
	public function biperSerialNumber(
		Request $request,
		string $clientId,
		CarteSimRepository $carteSimRepo,
		CommandeRepository $commandeRepo,
		LignesCommandeRepository $lignesCommandeRepo,
		EntityManagerInterface $entityManager
	): Response {
		$user = $this->getUser();
		$form = $this->createForm(CommandeValidationType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			$serialNumber = $data['serial_Number'];

			// Vérification de la carte SIM et si elle a déjà été achetée
			$carteSim = $this->getCarteSim($carteSimRepo, $serialNumber);
			if (!$carteSim) {
				return $this->redirectToRoute('biper_serial_number', ['clientId' => $clientId]);
			}

			// Vérifiez si la carte SIM a déjà été réservée
			if ($carteSim->isReserved()) { // Assurez-vous que cette méthode existe
				$this->addFlash('error', 'Cette carte SIM a déjà été réservée ou achetée.');
				return $this->redirectToRoute('biper_serial_number', ['clientId' => $clientId]);
			}

			$commande = $this->getCommande($commandeRepo, $clientId, $carteSim->getType());
			if (!$commande) {
				return $this->redirectToRoute('biper_serial_number', ['clientId' => $clientId]);
			}

			// Mettre à jour la commande
			$this->updateCommande($commande);

			// Créer une nouvelle ligne de commande
			$this->createLigneCommande($entityManager, $carteSim, $commande);

			// Mettre à jour la carte SIM
			$this->updateCarteSim($entityManager, $carteSim, $user);

			$this->addFlash('success', 'Ligne de commande créée avec succès et carte SIM réservée.');
		}

		// Retourner le rendu du template
		return $this->render('interfaces_admin/commandes_validation.html.twig', [
			'form' => $form->createView(),
		]);
	}

	private function getCarteSim(CarteSimRepository $carteSimRepo, string $serialNumber): ?CarteSim
	{
		$carteSim = $carteSimRepo->findOneBy(['serialNumber' => $serialNumber]);
		if (!$carteSim) {
			$this->addFlash('error', 'Carte SIM non trouvée.');
		}
		return $carteSim;
	}

	private function getCommande(CommandeRepository $commandeRepo, string $clientId, $typeCarteSim): ?Commande
	{
		$commandeEnCours = $commandeRepo->findOneBy([
			'code_client' => $clientId,
			'status' => 'en_cours',
			'simType' => $typeCarteSim->getNom(),
		], ['createdAt' => 'DESC']);

		if (!$commandeEnCours) {
			$commandeEnAttente = $commandeRepo->findOneBy([
				'code_client' => $clientId,
				'status' => 'en_attente',
				'simType' => $typeCarteSim->getNom(),
			], ['createdAt' => 'DESC']);

			if ($commandeEnAttente) {
				return $commandeEnAttente;
			} else {
				$this->addFlash('error', 'Aucune commande trouvée pour ce client.');
				return null;
			}
		}

		return $commandeEnCours;
	}

	private function updateCommande(Commande $commande): void
	{
		$qteval = $commande->getQtevalidee();
		$commande->setQtevalidee($qteval + 1);

		if ($commande->getQtevalidee() == $commande->getQte()) {
			$commande->setStatus('validee');
		} else {
			$commande->setStatus('en_cours');
		}
	}

	private function createLigneCommande(EntityManagerInterface $entityManager, CarteSim $carteSim, Commande $commande): void
	{
		$ligneCommande = new LignesCommande();
		$ligneCommande->setCartesSims($carteSim);
		$ligneCommande->setCommande($commande);
		$ligneCommande->setPrixUnitaire($carteSim->getType()->getPrix());
		$ligneCommande->setTypeSim($carteSim->getType());

		$entityManager->persist($ligneCommande);
	}

	private function updateCarteSim(EntityManagerInterface $entityManager, CarteSim $carteSim, $user): void
	{
		$carteSim->setReserved(true);
		$carteSim->setPurchasedBy($user);
		$entityManager->persist($carteSim);
		$entityManager->flush();
	}
}
