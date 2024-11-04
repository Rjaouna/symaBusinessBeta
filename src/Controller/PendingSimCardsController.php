<?php

namespace App\Controller;

use App\Entity\PendingSimCards;
use App\Form\PendingSimCardsType;
use App\Repository\PendingSimCardsRepository;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pending/sim/cards')]
final class PendingSimCardsController extends AbstractController
{
    #[Route(name: 'app_pending_sim_cards_index', methods: ['GET'])]
    public function index(PendingSimCardsRepository $pendingSimCardsRepository): Response
    {
        return $this->render('pending_sim_cards/index.html.twig', [
            'pending_sim_cards_migrated' => $pendingSimCardsRepository->findBy(['migrated' => 1]),
            'pending_sim_cards_not_migrated' => $pendingSimCardsRepository->findBy(['migrated' => 0]),
        ]);
    }

    #[Route('/new', name: 'app_pending_sim_cards_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SimTypeRepository $simTypeRepository): Response
    {
        $pendingSimCard = new PendingSimCards();
        $form = $this->createForm(
            PendingSimCardsType::class,
            $pendingSimCard,
            [
                'is_new' => true, // This is for creating a new entity
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Get the SimType object directly from the form data
            $simType = $pendingSimCard->getType(); // This should return the SimType object

            if ($simType) {
                // Set the type directly as a SimType object
                $pendingSimCard->setType($simType);
            } else {
                // Handle the case where the simType is not set, if necessary
            }
            $pendingSimCard->setImportedCsv(False);


            // Persist the new PendingSimCard entity
            $entityManager->persist($pendingSimCard);
            $entityManager->flush();

            return $this->redirectToRoute('app_pending_sim_cards_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pending_sim_cards/new.html.twig', [
            'pending_sim_card' => $pendingSimCard,
            'form' => $form->createView(), // Ensure to create the form view for rendering
        ]);
    }


    #[Route('/{id}', name: 'app_pending_sim_cards_show', methods: ['GET'])]
    public function show(PendingSimCards $pendingSimCard): Response
    {
        return $this->render('pending_sim_cards/show.html.twig', [
            'pending_sim_card' => $pendingSimCard,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pending_sim_cards_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PendingSimCards $pendingSimCard, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(
            PendingSimCardsType::class,
            $pendingSimCard,
            [
                'is_new' => false, // This is for editing an existing entity
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pending_sim_cards_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pending_sim_cards/edit.html.twig', [
            'pending_sim_card' => $pendingSimCard,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pending_sim_cards_delete', methods: ['POST'])]
    public function delete(Request $request, PendingSimCards $pendingSimCard, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pendingSimCard->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pendingSimCard);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_carte_sim_index', [], Response::HTTP_SEE_OTHER);
    }
}
