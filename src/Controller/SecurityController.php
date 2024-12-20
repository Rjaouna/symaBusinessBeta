<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private const SESSION_KEY = 'authentification_commercial';
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session): Response
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (!$session->isStarted()) {
            $session->start();
        }
        // Vérifier la présence du cookie
        $cookieValue = $session->get(self::SESSION_KEY);
        if (!$cookieValue) {
            // Si le cookie n'est pas présent, rediriger vers https://www.symamobile.com/
            $this->addFlash('warning', 'Vous devez être authentifié pour accéder à cette page.');
            return new RedirectResponse('https://symamobile.com/');
        }
        if ($this->getUser()) {
            return $this->redirectToRoute('app_syma_business');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/access-denied', name: 'access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('security/access_denied.html.twig');
    }
}
