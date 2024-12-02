<?php

namespace App\Controller;

use App\Service\ProfileCompletionService;
use App\Repository\BannerRepository;
use App\Repository\FactureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\Configuration\BusinessConfigChecker;
use phpDocumentor\Reflection\Types\Null_;
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
    public function index(BannerRepository $bannerRepository, FactureRepository $factureRepository): Response
    {
        if (!$this->businessConfigChecker->isConfigComplete()) {
            return $this->redirectToRoute('app_syma_business_config_new'); // Redirige vers la configuration
        }
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est bien connecté
        if (!$user) {
            // Rediriger vers une page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        

        // Récupérer toutes les factures pour l'utilisateur connecté
        $factures = $factureRepository->findBy(['client' => $user, 'seen' => Null]);
        $missingProperties = $this->profileCompletionService->checkProfileCompleteness($user);

        if (!empty($missingProperties) && !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addFlash('warning', 'Veuillez compléter votre profil. <a href="' . $this->generateUrl('app_profile_edit') . '">Cliquez ici pour compléter votre profil</a>.');
        }


        return $this->render('syma_business/index.html.twig', [
            'controller_name' => 'SymaBusinessController',
            'user' => $user,
            'missingProperties' => $missingProperties,
            'factures' => $factures,


        ]);
    }
}
