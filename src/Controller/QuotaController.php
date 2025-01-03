<?php

namespace App\Controller;

use App\Entity\Quota;
use App\Form\QuotaType;
use App\Repository\QuotaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[IsGranted('ROLE_ADMIN')]
#[Route('/quota')]
final class QuotaController extends AbstractController
{
    #[Route(name: 'app_quota_index', methods: ['GET'])]
    public function index(QuotaRepository $quotaRepository): Response
    {
        return $this->render('quota/index.html.twig', [
            'quotas' => $quotaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_quota_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le nombre actuel de quotas
        $quotaRepository = $entityManager->getRepository(Quota::class);
        $currentQuotaCount = $quotaRepository->count([]);

        // Vérifier si le maximum de quotas est atteint
        if ($currentQuotaCount >= 4) {
            $this->addFlash('warning', 'Vous ne pouvez pas créer plus de 4 quotas.');

            return $this->redirectToRoute('app_quota_index');
        }

        $quotum = new Quota();
        $form = $this->createForm(QuotaType::class, $quotum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Déterminer le code en fonction de l'ordre de création
            $codeMapping = [
                0 => 'first',
                1 => 'second',
                2 => 'third',
                3 => 'fourth',
            ];

            $quotum->setCode($codeMapping[$currentQuotaCount]);

            $entityManager->persist($quotum);
            $entityManager->flush();

            $this->addFlash('success', 'Quota créé avec succès.');

            return $this->redirectToRoute('app_quota_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quota/new.html.twig', [
            'quotum' => $quotum,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_quota_show', methods: ['GET'])]
    public function show(Quota $quotum): Response
    {
        return $this->render('quota/show.html.twig', [
            'quotum' => $quotum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quota_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quota $quotum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuotaType::class, $quotum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_quota_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quota/edit.html.twig', [
            'quotum' => $quotum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quota_delete', methods: ['POST'])]
    public function delete(Request $request, Quota $quotum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quotum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quotum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quota_index', [], Response::HTTP_SEE_OTHER);
    }
}
