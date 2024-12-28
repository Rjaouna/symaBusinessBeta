<?php

// src/Controller/GeolocController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class GeolocController extends AbstractController
{
    private $csrfTokenManager;
    private $entityManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $entityManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->entityManager = $entityManager;
    }


    #[Route('/geoloc/save', name: 'geoloc_save')]
    public function save(Request $request): JsonResponse
    {
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Utilisateur non authentifié.'], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifier si la requête est une requête AJAX
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => false, 'message' => 'Requête non valide.'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer le jeton CSRF depuis les en-têtes
        $csrfToken = $request->headers->get('X-CSRF-TOKEN');

        if (!$csrfToken) {
            return new JsonResponse(['success' => false, 'message' => 'Jeton CSRF manquant.'], Response::HTTP_BAD_REQUEST);
        }

        // Valider le jeton CSRF
        $token = new CsrfToken('geoloc', $csrfToken);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            return new JsonResponse(['success' => false, 'message' => 'Jeton CSRF invalide.'], Response::HTTP_FORBIDDEN);
        }

        // Lire et décoder le contenu JSON de la requête
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['success' => false, 'message' => 'Données JSON invalides.'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer les coordonnées
        $latitude = isset($data['latitude']) ? floatval($data['latitude']) : null;
        $longitude = isset($data['longitude']) ? floatval($data['longitude']) : null;

        // Valider les coordonnées
        if ($latitude === null || $longitude === null) {
            return new JsonResponse(['success' => false, 'message' => 'Coordonnées manquantes.'], Response::HTTP_BAD_REQUEST);
        }

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return new JsonResponse(['success' => false, 'message' => 'Coordonnées invalides.'], Response::HTTP_BAD_REQUEST);
        }

        // Enregistrer les coordonnées dans l'entité utilisateur
        $user->setLatitude($latitude);
        $user->setLongitude($longitude);

        // Persister les changements
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Retourner une réponse JSON de succès
        return new JsonResponse(['success' => true, 'message' => 'Position enregistrée avec succès.']);
    }
}
