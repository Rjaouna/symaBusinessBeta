<?php
// src/Controller/Admin/DashboardController.php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\SimType;
use App\Entity\EmailSettings;
use EasyCorp\Bundle\EasyAdminBundle\Field\Id;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class DashboardController extends AbstractDashboardController
{
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/administration/syma/dashboard', name: 'admin_syma_business')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Syma Business');
            
    }

    public function configureMenuItems(): iterable
    {
        // Ajouter un bouton pour rediriger vers la page d'accueil avec une classe personnalisée
        yield MenuItem::linkToUrl('Retour à l\'accueil', 'fas fa-home', $this->generateUrl('app_syma_business'))
        ->setCssClass('menu-return-home'); // Utilisez setCssClass pour appliquer une classe CSS

        // Créer un menu principal "Clients"
        yield MenuItem::section('Clients', 'fas fa-users'); // Titre du menu principal

        // Sous-menu pour "Liste des clients"
        yield MenuItem::linkToCrud('Liste des clients', 'fas fa-list', User::class);

        // Sous-menu pour "Ajouter un client"
        yield MenuItem::linkToCrud('Ajouter un client', 'fas fa-plus', User::class)
            ->setAction('new'); // Cela redirige vers la création d'un nouvel utilisateur




        yield MenuItem::section('Types cartes Sim', 'fas fa-sim-card'); // Titre du menu principal

        yield MenuItem::linkToCrud('Types cartes Sim', 'fas fa-list', SimType::class);
        // Sous-menu pour "Ajouter un client"
        yield MenuItem::linkToCrud('Ajouter un type', 'fas fa-plus', SimType::class)
            ->setAction('new'); // Cela redirige vers la création d'un nouvel utilisateur




    }

   
}
