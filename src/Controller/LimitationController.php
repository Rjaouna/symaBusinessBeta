<?php

namespace App\Controller;

use App\Entity\Limitation;
use App\Form\LimitationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/limitation')]
final class LimitationController extends AbstractController
{
    #[Route(name: 'app_limitation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $limitations = $entityManager
            ->getRepository(Limitation::class)
            ->findAll();

        return $this->render('limitation/index.html.twig', [
            'limitations' => $limitations,
        ]);
    }

    #[Route('/new', name: 'app_limitation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $limitation = new Limitation();
        $form = $this->createForm(LimitationType::class, $limitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($limitation);
            $entityManager->flush();

            return $this->redirectToRoute('app_limitation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('limitation/new.html.twig', [
            'limitation' => $limitation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_limitation_show', methods: ['GET'])]
    public function show(Limitation $limitation): Response
    {
        return $this->render('limitation/show.html.twig', [
            'limitation' => $limitation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_limitation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Limitation $limitation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LimitationType::class, $limitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_limitation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('limitation/edit.html.twig', [
            'limitation' => $limitation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_limitation_delete', methods: ['POST'])]
    public function delete(Request $request, Limitation $limitation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $limitation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($limitation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_limitation_index', [], Response::HTTP_SEE_OTHER);
    }
}
