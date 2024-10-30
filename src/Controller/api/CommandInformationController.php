<?php
// src/Controller/ApiCommandController.php

namespace App\Controller\api;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\CommandeRepository;

class CommandInformationController extends AbstractController
{
	public function __construct(private SerializerInterface $serializer) {}

	#[Route('/api/commande', name: 'api_commandes_information', methods: ['GET'])]
	public function getCommandes(CommandeRepository $commandeRepository): JsonResponse
	{
		// Récupérer toutes les commandes avec leur statut et code client
		$commandes = $commandeRepository->findAll();

		// Sérialiser les commandes en JSON avec le groupe 'commande_info'
		$data = $this->serializer->serialize($commandes, 'json', ['groups' => 'user_info']);

		return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
	}
}
