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
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[IsGranted('ROLE_ADMIN')]
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
