<?php
// src/Controller/ProfileController.php

namespace App\Controller\InterfacesUsers;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/client')]
class ProfileController extends AbstractController
{
	#[Route('/profile', name: 'app_profile')]
	public function index(): Response
	{
		return $this->render('interfaces_users/profile/index.html.twig', [
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

		return $this->render('interfaces_users/profile/edit.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
