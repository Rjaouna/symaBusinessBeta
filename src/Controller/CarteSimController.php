<?php

namespace App\Controller;

use App\Service\SerialNumberComparator;
use App\Entity\CarteSim;
use App\Form\CarteSimType;
use App\Repository\CarteSimRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[IsGranted('ROLE_ADMIN')]
#[Route('/carte/sim')]
final class CarteSimController extends AbstractController
{
    private SerialNumberComparator $serialNumberComparator;
    public function __construct(SerialNumberComparator $serialNumberComparator)
    {
        $this->serialNumberComparator = $serialNumberComparator;
    }
    #[Route(name: 'app_carte_sim_index', methods: ['GET'])]
    public function index(CarteSimRepository $carteSimRepository): Response
    {
        $this->serialNumberComparator->compareSerialNumbers();

        return $this->render('carte_sim/index.html.twig', [
            'carte_simsreserved' => $carteSimRepository->findAll(),
            'carte_sims' => $carteSimRepository->findBy(['reserved' => 0]),
            'hasMissingSerialNumbers' =>  $this->serialNumberComparator->compareSerialNumbers()
        ]);
    }

    #[Route('/new', name: 'app_carte_sim_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $carteSim = new CarteSim();
        $form = $this->createForm(CarteSimType::class, $carteSim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($carteSim);
            $entityManager->flush();

            return $this->redirectToRoute('app_carte_sim_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carte_sim/new.html.twig', [
            'carte_sim' => $carteSim,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carte_sim_show', methods: ['GET'])]
    public function show(CarteSim $carteSim): Response
    {
        return $this->render('carte_sim/show.html.twig', [
            'carte_sim' => $carteSim,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_carte_sim_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CarteSim $carteSim, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarteSimType::class, $carteSim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_carte_sim_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carte_sim/edit.html.twig', [
            'carte_sim' => $carteSim,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carte_sim_delete', methods: ['POST'])]
    public function delete(Request $request, CarteSim $carteSim, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carteSim->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($carteSim);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_carte_sim_index', [], Response::HTTP_SEE_OTHER);
    }
}
