<?php

namespace App\Controller\Configuration;

use App\Entity\SymaBusinessConfig;
use App\Form\SymaBusinessConfigType;
use App\Repository\SymaBusinessConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/syma/business/config')]
final class SymaBusinessConfigController extends AbstractController
{
    #[Route('/', name: 'app_syma_business_config_index', methods: ['GET'])]
    public function index(SymaBusinessConfigRepository $symaBusinessConfigRepository): Response
    {
        return $this->render('syma_business_config/index.html.twig', [
            'syma_business_configs' => $symaBusinessConfigRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_syma_business_config_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $symaBusinessConfig = new SymaBusinessConfig();
        $form = $this->createForm(SymaBusinessConfigType::class, $symaBusinessConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($symaBusinessConfig);
            $entityManager->flush();

            return $this->redirectToRoute('app_syma_business', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('syma_business_config/new.html.twig', [
            'syma_business_config' => $symaBusinessConfig,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_syma_business_config_show', methods: ['GET'])]
    public function show(SymaBusinessConfig $symaBusinessConfig): Response
    {
        return $this->render('syma_business_config/show.html.twig', [
            'syma_business_config' => $symaBusinessConfig,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_syma_business_config_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SymaBusinessConfig $symaBusinessConfig, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SymaBusinessConfigType::class, $symaBusinessConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_syma_business_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('syma_business_config/edit.html.twig', [
            'syma_business_config' => $symaBusinessConfig,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_syma_business_config_delete', methods: ['POST'])]
    public function delete(Request $request, SymaBusinessConfig $symaBusinessConfig, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $symaBusinessConfig->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($symaBusinessConfig);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_syma_business_config_index', [], Response::HTTP_SEE_OTHER);
    }
}
