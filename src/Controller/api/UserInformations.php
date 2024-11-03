<?php

namespace App\Controller\api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class UserInformations extends AbstractController
{
	private SerializerInterface $serializer;

	public function __construct(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
	}

	#[Route('/api/user', name: 'api_user_information', methods: ['GET'])]
	public function getUserInformation(): JsonResponse
	{
		// Récupère l'utilisateur connecté
		$user = $this->getUser();

		if (!$user instanceof User) {
			return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_UNAUTHORIZED);
		}

		// Serialize user data
		$data = $this->serializer->serialize($user, 'json', ['groups' => 'user_info']);

		return new JsonResponse($data, JsonResponse::HTTP_OK, [], true); // true indicates that the response is already JSON
	}

	#[Route('/api/users', name: 'api_users_list', methods: ['GET'])]
	public function getUsersList(UserRepository $user): JsonResponse
	{
		// Récupère tous les utilisateurs
		$users = $user->findAll();

		// Vérifie si des utilisateurs existent
		if (empty($users)) {
			return new JsonResponse(['error' => 'No users found'], JsonResponse::HTTP_NOT_FOUND);
		}

		// Serialize user data
		$data = $this->serializer->serialize($users, 'json', ['groups' => 'user_info']);

		return new JsonResponse($data, JsonResponse::HTTP_OK, [], true); // true indique que la réponse est déjà en JSON
	}

	#[Route('/api/users/count/no-code-client', name: 'api_users_count_no_code_client', methods: ['GET'])]
	public function getUsersCountWithoutCodeClientApi(UserRepository $user): JsonResponse
	{
		// Récupère tous les utilisateurs
		$users = $user->findAll();

		// Filtrer les utilisateurs sans code_client
		$usersWithoutCodeClient = array_filter($users, function ($user) {
			return $user->getCodeClient() === null;
		});

		// Compte le nombre d'utilisateurs sans code_client
		$count = count($usersWithoutCodeClient);

		// Récupérer les noms des responsables des utilisateurs sans code_client
		$responsableNames = array_map(function ($user) {
			return $user->getNomResponsable(); // Assurez-vous que cette méthode existe
		}, $usersWithoutCodeClient);

		return new JsonResponse(['count' => $count, 'responsableNames' => $responsableNames], JsonResponse::HTTP_OK);
	}

	#[Route('/users/count/no-code-client', name: 'users_count_no_code_client', methods: ['GET'])]
	public function getUsersCountWithoutCodeClient(UserRepository $user): Response
	{
		// Récupère tous les utilisateurs
		$users = $user->findBy(['codeClient' => Null]);
		return $this->render('interfaces_admin/users_count_no_code_client/index.html.twig', [
			'users' => $users
		]);
	}



}
