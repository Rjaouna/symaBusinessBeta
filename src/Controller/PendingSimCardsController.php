<?php

namespace App\Controller;

use App\Entity\Chapelet;
use App\Entity\PendingSimCards;
use App\Form\PendingSimCardsType;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PendingSimCardsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
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

    

    #[Route('/{id}', name: 'app_pending_sim_cards_show', methods: ['GET'])]
    public function show(PendingSimCards $pendingSimCard): Response
    {
        return $this->render('pending_sim_cards/show.html.twig', [
            'pending_sim_card' => $pendingSimCard,
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
