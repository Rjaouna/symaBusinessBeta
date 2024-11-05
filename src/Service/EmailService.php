<?php
// src/Service/EmailService.php
namespace App\Service;

use App\Entity\User; // Assurez-vous d'importer l'entité User
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService
{
	private MailerInterface $mailer;
	private Environment $twig; // Pour utiliser Twig

	public function __construct(MailerInterface $mailer, Environment $twig)
	{
		$this->mailer = $mailer;
		$this->twig = $twig;
	}

	public function sendQuotaChangeEmail(User $user, string $template, array $context): void
	{
		// Générez le message à partir d'un template Twig
		$message = $this->twig->render($template, $context);

		// Créez l'email
		$email = (new Email())
			->from('contact@cartemenu.fr')
			->to($user->getEmail()) // Récupérez l'email de l'utilisateur
			->subject('Syma Business - Quota info !')
			->html($message); // Utilisez html si vous envoyez un email HTML

		// Envoyez l'email
		$this->mailer->send($email);
	}
}
