<?php
// src/Controller/UserCommandController.php

namespace App\Controller\Management;

use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_ADMIN')]
class UserCommandeWithOrdersController extends AbstractController
{
	#[Route('/admin/users-with-orders', name: 'users_with_orders')]
	public function index(
		UserRepository $userRepository,
		CommandeRepository $commandeRepository
	): Response {
		// Récupérer tous les utilisateurs
		$users = $userRepository->findAll();

		// Filtrer les utilisateurs ayant des commandes en cours ou en attente
		$usersWithOrders = [];
		foreach ($users as $user) {
			$commandes = $commandeRepository->findBy([
				'user' => $user,
				'status' => ['en_cours', 'en_attente']
			]);

			if (!empty($commandes)) {
				$usersWithOrders[] = [
					'user' => $user,
					'commandes' => $commandes
				];
			}
		}

		return $this->render('interfaces_admin/user_commande_with_orders/index.html.twig', [
			'usersWithOrders' => $usersWithOrders,
		]);
	}
}
