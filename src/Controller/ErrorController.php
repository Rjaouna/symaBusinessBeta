<?php
// src/Controller/ErrorController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ErrorController extends AbstractController
{
	#[Route('/404', name: 'error_404')]
	public function notFound(): Response
	{
		// Si vous avez besoin d'injecter des services, vous pouvez le faire dans le constructeur
		// Par exemple : $quotaRepository = $this->getDoctrine()->getRepository(Quota::class);

		return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
	}
}
