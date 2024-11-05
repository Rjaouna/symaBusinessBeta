<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\SimType;
use App\Entity\EmailSettings;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin_syma_business')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Syma Business');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Syma Business dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Clients', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Types cartes Sim', 'fas fa-users', SimType::class);
        yield MenuItem::linkToCrud('Configuration des E-mails', 'fa fa-reply-all', EmailSettings::class);
        
    }
}
