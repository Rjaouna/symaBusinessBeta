<?php
// src/Controller/MailerController.php
namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerController extends AbstractController
{
	#[Route('/email', name: 'send_email')]
	public function sendEmail(MailerInterface $mailer): Response
	{
		// Création de l'e-mail
		$email = (new Email())
			->from('hello@example.com')
			->to('you@example.com')
			->subject('Time for Symfony Mailer!')
			->text('Sending emails is fun again!')
			->html('<p>See Twig integration for better HTML integration!</p>');

		// Tentative d'envoi avec gestion des erreurs
		try {
			$mailer->send($email);
			return new Response('E-mail envoyé avec succès !', Response::HTTP_OK);
		} catch (TransportExceptionInterface $e) {
			return new Response('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
