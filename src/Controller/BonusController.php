<?php

namespace App\Controller;

use App\Entity\Bonus;
use App\Form\BonusType;
use App\Repository\UserRepository;
use App\Repository\BonusRepository;
use App\Repository\QuotaRepository;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/bonus')]
final class BonusController extends AbstractController
{
    #[Route(name: 'app_bonus_index', methods: ['GET'])]
    public function index(BonusRepository $bonusRepository): Response
    {
        // Rendre la vue d'index des bonus avec tous les bonus disponibles
        return $this->render('bonus/index.html.twig', [
            'bonuses' => $bonusRepository->findAll(),
        ]);
    }

    #[Route('/gestion-bonus', name: 'app_gestion_bonus')]
    public function gestion(SimTypeRepository $simTypeRepository, QuotaRepository $quotaRepository): Response
    {
        // Récupérer tous les types de cartes SIM depuis la base de données
        $typesCartesSim = $simTypeRepository->findAll();

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer le total des bonus de l'utilisateur
        $totalBonus = $user->getTotalBonus();

        // Récupérer le quota de l'utilisateur
        $quota = $user->getQuotas();
        if ($quota) {
            // Si l'utilisateur a un quota, récupérer ses informations
            $idQuota = $quota->getId(); // Récupérer l'ID du quota
            $quotasUser = $quotaRepository->find($idQuota); // Récupérer l'objet Quota à partir de l'ID

            // Accéder aux propriétés du quota via des getters
            $quotaSim5Quota = $quotasUser->getSim5Quota();
            $quotaSim10Quota = $quotasUser->getSim10Quota();
            $quotaSim15Quota = $quotasUser->getSim15Quota(); // Correction de l'appel au getter
            $quotaSim20Quota = $quotasUser->getSim20Quota();
        } else {
            // Gérer le cas où l'utilisateur n'a pas de quota
            $quotasUser = null;
            $quotaNom = null;
            $quotaSim5Quota = null;
            $quotaSim10Quota = null;
            $quotaSim15Quota = null;
            $quotaSim20Quota = null;
        }

        // Créer un tableau pour stocker les usages des cartes SIM
        $usages = [];

        // Remplir le tableau avec les usages en fonction des types de cartes SIM
        foreach ($typesCartesSim as $typeCarte) {
            switch ($typeCarte->getCode()) {
                case 'cartesim05':
                    $usages[$typeCarte->getNom()] = $user->getSim5Usage();
                    break;

                case 'cartesim10':
                    $usages[$typeCarte->getNom()] = $user->getSim10Usage();
                    break;

                case 'cartesim15':
                    $usages[$typeCarte->getNom()] = $user->getSim15Usage();
                    break;

                case 'cartesim20':
                    $usages[$typeCarte->getNom()] = $user->getSim20Usage();
                    break;

                default:
                    // Optionnel : gérer les types non reconnus
                    $usages[$typeCarte->getNom()] = null; // ou une valeur par défaut
                    break;
            }
        }

        // Rendre la vue de gestion des bonus avec les données nécessaires
        return $this->render('bonus/user/gestion.html.twig', [
            'user' => $user,
            'typesCartesSim' => $typesCartesSim,
            'totalBonus' => $totalBonus,
            'usages' => $usages,
            'quotaSim5Quota' => $quotaSim5Quota,
            'quotaSim10Quota' => $quotaSim10Quota,
            'quotaSim15Quota' => $quotaSim15Quota,
            'quotaSim20Quota' => $quotaSim20Quota,
        ]);
    }

    #[Route('/sim/usage/update', name: 'sim_usage_update')]
    public function updateSimUsage(Request $request, EntityManagerInterface $entityManager, SimTypeRepository $simTypeRepository): Response
    {
        // Récupération de l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('login'); // Redirection vers la page de connexion
        }

        // Récupération des paramètres de la requête
        $remainingBonus = $request->query->get('remainingBonus');
        $orderItemsJson = $request->query->get('orderItems');

        // Décoder le JSON pour obtenir les articles de commande
        $orderItems = json_decode($orderItemsJson, true);

        // Vérification des données envoyées
        if (!is_array($orderItems)) {
            return $this->redirectToRoute('error_page', ['message' => 'Données invalides, orderItems est requis']);
        }

        // Traitement des articles commandés
        foreach ($orderItems as $item) {
            if (!isset($item['typeCarte']) || !isset($item['quantity']) || $item['quantity'] <= 0) {
                return $this->redirectToRoute('error_page', ['message' => 'Type de carte ou quantité manquante']);
            }

            $typeCarte = $item['typeCarte'];
            $quantity = (int)$item['quantity'];

            // Récupération du type de SIM à partir de la base de données
            $simTypeEntity = $simTypeRepository->findOneBy(['nom' => $typeCarte]);
            $code = strtolower($simTypeEntity->getCode());

            if (!$code) {
                return $this->redirectToRoute('error_page', ['message' => 'Type de SIM invalide : ' . $typeCarte]);
            }

            $currentSimUsage = null;

            switch ($code) {
                case 'cartesim05':
                    $currentSimUsage = $user->getSim5Usage();
                    break;


                case 'cartesim10':
                    $currentSimUsage = $user->getSim10Usage();

                    break;

                case 'cartesim15':
                    $currentSimUsage = $user->getSim15Usage();
                    break;

                case 'cartesim20':
                    $currentSimUsage = $user->getSim20Usage();
                    break;

                default:
                    return $this->redirectToRoute('app_gestion_bonus', ['message' => 'Type de SIM invalide : ' . $typeCarte]);
            }

            // Vérification de la quantité demandée par rapport à la consommation disponible
            if ($currentSimUsage < $quantity) {
                $this->addFlash('danger', 'Quantité demandée supérieure à la consommation disponible pour ' . $typeCarte);
                return $this->redirectToRoute('app_gestion_bonus');
            }

            // Mise à jour de la consommation
            switch ($simTypeEntity->getCode()) {
                case 'cartesim05':
                    $user->setSim5Usage($currentSimUsage - $quantity);
                    break;

                case 'cartesim10':
                    $user->setSim10Usage($currentSimUsage - $quantity);
                    break;

                case 'cartesim15':
                    $user->setSim15Usage($currentSimUsage - $quantity);
                    break;

                case 'cartesim20':
                    $user->setSim20Usage($currentSimUsage - $quantity);
                    break;
            }
        }

        $user->setTotalBonus($remainingBonus);

        // Sauvegarde dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // Redirection vers une page de succès
        return $this->redirectToRoute('app_gestion_bonus', [
            'message' => 'Consommation mise à jour avec succès',
            'remainingBonus' => $remainingBonus
        ]);
    }

    #[Route('/new', name: 'app_bonus_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $bonu = new Bonus();
        $form = $this->createForm(BonusType::class, $bonu);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'ID de l'utilisateur à partir du formulaire
            $userId = $form->get('user')->getData(); // assurez-vous que le formulaire a un champ 'user' qui est un ID utilisateur
            // Rechercher l'utilisateur à partir de son ID
            $user = $userRepository->find($userId);
            if ($user) {
                // Associer le bonus à l'utilisateur
                $bonu->setUser($user);

                // Ajouter la valeur du nouveau bonus au total actuel
                $newBonusValue = (int) $bonu->getValeur();
                $currentTotalBonus = $user->getTotalBonus();
                $user->setTotalBonus($currentTotalBonus + $newBonusValue);

                // Persister le bonus et mettre à jour l'utilisateur
                $entityManager->persist($bonu);
                $entityManager->flush();

                // Redirection après l'ajout du bonus
                return $this->redirectToRoute('app_bonus_index', [], Response::HTTP_SEE_OTHER);
            } else {
                // Si aucun utilisateur n'a été trouvé, vous pouvez ajouter un message d'erreur
                $this->addFlash('error', 'Utilisateur non trouvé avec cette adresse email.');
            }
        }

        return $this->render('bonus/new.html.twig', [
            'bonu' => $bonu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bonus_show', methods: ['GET'])]
    public function show(Bonus $bonu): Response
    {
        return $this->render('bonus/show.html.twig', [
            'bonu' => $bonu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_bonus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bonus $bonu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BonusType::class, $bonu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_bonus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bonus/edit.html.twig', [
            'bonu' => $bonu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bonus_delete', methods: ['POST'])]
    public function delete(Request $request, Bonus $bonu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $bonu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($bonu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_bonus_index', [], Response::HTTP_SEE_OTHER);
    }
}
