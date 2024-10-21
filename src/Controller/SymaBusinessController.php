<?php

namespace App\Controller;

use App\Service\ProfileCompletionService;
use App\Repository\BannerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\Configuration\BusinessConfigChecker;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SymaBusinessController extends AbstractController
{
    private $businessConfigChecker;
    private ProfileCompletionService $profileCompletionService;

    public function __construct(BusinessConfigChecker $businessConfigChecker, ProfileCompletionService $profileCompletionService)
    {
        $this->businessConfigChecker = $businessConfigChecker;
        $this->profileCompletionService = $profileCompletionService;
    }
    
    #[IsGranted('ROLE_USER')]
    #[Route('', name: 'app_syma_business')]
    public function index(BannerRepository $bannerRepository): Response
    {
        if (!$this->businessConfigChecker->isConfigComplete()) {
            return $this->redirectToRoute('app_syma_business_config_new'); // Redirige vers la configuration
        }
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $missingProperties = $this->profileCompletionService->checkProfileCompleteness($user);

        if (!empty($missingProperties) && !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addFlash('warning', 'Veuillez compléter votre profil. <a href="' . $this->generateUrl('app_profile_edit') . '">Cliquez ici pour compléter votre profil</a>.');
        }


        return $this->render('syma_business/index.html.twig', [
            'controller_name' => 'SymaBusinessController',
            'user' => $user,
            'missingProperties' => $missingProperties,


        ]);
    }
}
