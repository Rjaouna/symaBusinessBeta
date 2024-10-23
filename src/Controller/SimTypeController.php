<?php

namespace App\Controller;

use App\Entity\SimType;
use App\Form\SimTypeType;
use App\Repository\SimTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sim/type')]
final class SimTypeController extends AbstractController
{
    #[Route(name: 'app_sim_type_index', methods: ['GET'])]
    public function index(SimTypeRepository $simTypeRepository): Response
    {
        return $this->render('sim_type/index.html.twig', [
            'sim_types' => $simTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sim_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $simType = new SimType();
        $form = $this->createForm(SimTypeType::class, $simType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($simType);
            $entityManager->flush();

            return $this->redirectToRoute('app_sim_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sim_type/new.html.twig', [
            'sim_type' => $simType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sim_type_show', methods: ['GET'])]
    public function show(SimType $simType): Response
    {
        return $this->render('sim_type/show.html.twig', [
            'sim_type' => $simType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sim_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SimType $simType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SimTypeType::class, $simType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sim_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sim_type/edit.html.twig', [
            'sim_type' => $simType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sim_type_delete', methods: ['POST'])]
    public function delete(Request $request, SimType $simType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$simType->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($simType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sim_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
