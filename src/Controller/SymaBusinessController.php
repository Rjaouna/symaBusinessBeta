<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SymaBusinessController extends AbstractController
{
    #[Route('', name: 'app_syma_business')]
    public function index(): Response
    {
        return $this->render('syma_business/index.html.twig', [
            'controller_name' => 'SymaBusinessController',
        ]);
    }
}
