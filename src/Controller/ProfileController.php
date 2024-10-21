<?php
// src/Controller/ProfileController.php

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
	#[Route('/profile', name: 'app_profile')]
	public function index(): Response
	{
		return $this->render('profile/index.html.twig', [
			'user' => $this->getUser(), // Récupérer l'utilisateur connecté
		]);
	}

	#[Route('/profile/edit', name: 'app_profile_edit')]
	public function edit(Request $request, EntityManagerInterface $entityManager): Response
	{
		$user = $this->getUser(); // Récupérer l'utilisateur connecté

		// Créer le formulaire pour éditer l'utilisateur
		$form = $this->createForm(ProfileType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Enregistrer les modifications
			$entityManager->flush();


			// Rediriger vers le profil après l'édition
			return $this->redirectToRoute('app_profile');
		}

		return $this->render('profile/edit.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
