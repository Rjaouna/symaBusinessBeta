<?php

namespace App\Controller;

use App\Repository\BannerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\Configuration\BusinessConfigChecker;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SymaBusinessController extends AbstractController
{
    private $businessConfigChecker;

    public function __construct(BusinessConfigChecker $businessConfigChecker)
    {
        $this->businessConfigChecker = $businessConfigChecker;
    }
    #[IsGranted('ROLE_USER')]
    #[Route('', name: 'app_syma_business')]
    public function index(BannerRepository $bannerRepository): Response
    {
        if (!$this->businessConfigChecker->isConfigComplete()) {
            return $this->redirectToRoute('app_syma_business_config_new'); // Redirige vers la configuration
        }

        return $this->render('syma_business/index.html.twig', [
            'controller_name' => 'SymaBusinessController',


        ]);
    }
}
