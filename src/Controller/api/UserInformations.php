<?php

namespace App\Controller\api;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
