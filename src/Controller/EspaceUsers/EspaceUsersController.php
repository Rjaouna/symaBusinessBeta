<?php

namespace App\Controller\EspaceUsers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
class EspaceUsersController extends AbstractController
{
    #[Route('/espace/users', name: 'app_espace_users')]
    public function index(): Response
    {
        return $this->render('Espace_users/index.html.twig', [
            'controller_name' => 'EspaceUsersController',
        ]);
    }
}
