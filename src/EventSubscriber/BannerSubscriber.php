<?php
// src/EventSubscriber/BannerSubscriber.php
namespace App\EventSubscriber;

use App\Service\BannerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class BannerSubscriber implements EventSubscriberInterface
{
	private $bannerService;
	private $twig;

	public function __construct(BannerService $bannerService, Environment $twig)
	{
		$this->bannerService = $bannerService;
		$this->twig = $twig;
	}

	public function onKernelController(ControllerEvent $event)
	{
		$banners = $this->bannerService->getBanners();
		// Ajoute les bannières à la variable globale Twig
		$this->twig->addGlobal('banners', $banners);
	}

	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::CONTROLLER => 'onKernelController',
		];
	}
}
