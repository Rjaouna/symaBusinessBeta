<?php

namespace App\Controller;

use App\Entity\Tournee;
use App\Entity\User;
use App\Form\TourneeType;
use App\Repository\CommercialRepository;
use App\Repository\TourneeRepository;
use App\Repository\UserRepository;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\DistanceCalculator;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/tournee')]
class TourneeController extends AbstractController
{

	#[Route('/', name: 'tournee_index', methods: ['GET'])]
	public function index(TourneeRepository $tourneeRepository, CommercialRepository $commercialRepository): Response
	{
		$user = $this->getUser();
		$commercial = $commercialRepository->findBy(['email' => $user->getEmail()]);
		if ($commercial) {
			$userActive = $commercial[0]->getId();
		}


		$tournees = [];

		if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SUPER_ADMIN')) {
			// Administrateurs voient toutes les tournées
			$tournees = $tourneeRepository->findAll();
		} elseif ($this->isGranted('ROLE_COMMERCIAL')) {
			// Commerciaux voient uniquement leurs tournées
			$tournees = $tourneeRepository->findBy(['commercial' => $userActive]);
		} else {
			// Autres rôles ou non authentifié : liste vide ou redirection
			$tournees = [];
			// Optionnel : throw $this->createAccessDeniedException('Accès refusé');
		}
		return $this->render('tournee/index.html.twig', [
			'tournees' => $tournees,
		]);
	}

	#[Route('/new', name: 'tournee_new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager, ZoneRepository $zoneRepository): Response
	{
		$tournee = new Tournee();
		$form = $this->createForm(TourneeType::class, $tournee);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Assigner automatiquement les clients à la tournée
			$zone = $tournee->getZone();
			$zoneReserved = $zoneRepository->find($zone);

			if ($zoneReserved->getStatut() === 'encours') {
				$this->addFlash('warning', 'Cette zone est deja en cours');
				return $this->redirectToRoute('tournee_index', [], Response::HTTP_SEE_OTHER);
			}
			$clients = $entityManager->getRepository(User::class)->findBy(['codeZone' => $zone]);
			if (empty($clients)) {
				// Aucun client trouvé pour cette zone
				$this->addFlash('warning', 'Aucun client trouvé pour la zone sélectionnée.');
				return $this->redirectToRoute('tournee_index', [], Response::HTTP_SEE_OTHER);
			}

			foreach ($clients as $client) {
				$tournee->addClient($client);
				$client->setDelivered(false);
			}
			$tournee->setStatus('pending');
			$zone->setStatut('encours');

			$entityManager->persist($zone);
			$entityManager->persist($tournee);
			$entityManager->flush();

			return $this->redirectToRoute('tournee_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('tournee/new.html.twig', [
			'tournee' => $tournee,
			'form' => $form->createView(),
		]);
	}

	#[Route('/{id}/livrer', name: 'tournee_livrer', methods: ['POST'])]
	public function livrer(Request $request, Tournee $tournee, EntityManagerInterface $entityManager): Response
	{
		$clientId = $request->request->get('client_id');
		$csrfToken = $request->request->get('_csrf_token');

		if (!$this->isCsrfTokenValid('livrer_tournee', $csrfToken)) {
			throw $this->createAccessDeniedException('Jeton CSRF invalide.');
		}

		$client = $entityManager->getRepository(User::class)->find($clientId);
		if (!$client || !$tournee->getClients()->contains($client)) {
			$this->addFlash('error', 'Client invalide.');
			return $this->redirectToRoute('tournee_show', ['id' => $tournee->getId()]);
		}

		// Logique pour marquer la livraison
		$client->setDelivered(true); // Assurez-vous que l'entité User a cette propriété et méthode
		$client->setTournee(Null);
		$entityManager->persist($client);

		// Vérifiez si tous les clients sont livrés
		$allDelivered = true;
		foreach ($tournee->getClients() as $c) {
			if (!$c->isDelivered()) { // Assurez-vous que l'entité User a cette méthode
				$allDelivered = false;
				break;
			}
		}

		if ($allDelivered) {
			$tournee->setCompleted(true);
			$entityManager->persist($tournee);
		}

		$entityManager->flush();

		$this->addFlash('success', 'Livraison enregistrée avec succès.');
		return $this->redirectToRoute('tournee_index');
	}

	#[Route('/tournee/{id}', name: 'tournee_show', methods: ['GET'])]
	public function show(Tournee $tournee, UserRepository $clientRepository, DistanceCalculator $distanceCalculator): Response
	{
		$clients = $clientRepository->findAll();
		// Vos coordonnées (stockées dans les paramètres)
		$userLatitude = $this->getUser()->getLatitude();
		$userLongitude = $this->getUser()->getLongitude();
		// Calculer la distance pour chaque client
		foreach ($clients as $client) {
			if ($client->getLatitude() && $client->getLongitude()) {
				$distance = $distanceCalculator->calculateDistance(
					$userLatitude,
					$userLongitude,
					$client->getLatitude(),
					$client->getLongitude()
				);
				$client->setDistanceFromUser($distance); // Vous devez ajouter ce champ virtuel ou gérer autrement
			} else {
				$client->setDistanceFromUser(null);
			}
		}

		return $this->render('tournee/show.html.twig', [
			'tournee' => $tournee,
		]);
	}

	#[Route('/tournee/{id}/delete', name: 'tournee_delete', methods: ['POST'])]
	public function delete(Tournee $tournee, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
	{
		$submittedToken = $request->request->get('_token');

		// Vérifie la validité du jeton CSRF
		if ($this->isCsrfTokenValid('delete' . $tournee->getId(), $submittedToken)) {
			// Vérifie si la tournée est complétée
			if ($tournee->isCompleted()) {
				$zone = $tournee->getZone();
				$zone->setStatut(null);
				$entityManager->persist($zone);
				$entityManager->remove($tournee);
				$entityManager->flush();
				// Envoi de l'email
				$email = (new Email())
					->from('contact@cartemenu.fr') // Adresse de l'expéditeur
					->to('promobile59810@gmail.com') // Adresse du destinataire
					->subject('Tournée efféctuée et clutorée')
					->html(
						'<p>Bonjour,</p>' .
							'<p>La tournée de la <strong>Zone  ' . $tournee->getZone()->getNom() . '</strong> a été efféctuée et cloturée avec succès.</p>' .
							'<p><strong>Zone :</strong> ' . $tournee->getZone()->getNom() . '</p>' .
							'<p><strong>Commercial :</strong> ' . $tournee->getCommercial()->getNom() . '</p>' .
							'<p>Cordialement,</p>' .
							'<p>L\'équipe de gestion</p>'
					);


				try {
					$mailer->send($email);
					$this->addFlash('success', 'La tournée a été cloturée avec succès. Un email de notification a été envoyé.');
				} catch (\Exception $e) {
					$this->addFlash('error', 'La tournée a été cloturée, mais l\'email de notification n\'a pas pu être envoyé.');
				}
			} else {
				$this->addFlash('error', 'Impossible de cloturer la tournée car elle n\'est pas complétée.');
			}
		} else {
			$this->addFlash('error', 'Jeton de sécurité invalide. La suppression a échoué.');
		}


		return $this->redirectToRoute('tournee_index');
	}
}
