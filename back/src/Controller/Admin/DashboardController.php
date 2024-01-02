<?php

namespace App\Controller\Admin;

use App\Entity\User; 
use App\Entity\Children; 
use App\Entity\Availability; 
use App\Entity\Chat; 
use App\Entity\ChatMessage; 

use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\StatisticsController;
use App\Entity\Comment;
use App\Entity\Order;
use App\Entity\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Projet 05 Obaby Back')
            ->disableUrlSignatures()
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {   
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Stats');
        yield MenuItem::linkToRoute('Statistiques', 'fas fa-chart-bar', 'admin_statistics');
        yield MenuItem::section('Gestion des utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Enfants', 'fas fa-list', Children::class);
        yield MenuItem::linkToCrud('Disponibilités', 'fas fa-list', Availability::class);
        yield MenuItem::linkToCrud('Commentaires', 'fas fa-list', Comment::class);

        yield MenuItem::section('Gestion des abonnements');
        yield MenuItem::linkToCrud('Abonnements', 'fas fa-list', Subscription::class);
        yield MenuItem::linkToCrud('Factures', 'fas fa-list', Order::class);
    
        yield MenuItem::section('Gestion de la messagerie');
        yield MenuItem::linkToCrud('Chat', 'fas fa-list', Chat::class);
        yield MenuItem::linkToCrud('ChatMessage', 'fas fa-list', ChatMessage::class);

        yield MenuItem::section('Liens utiles');
        yield MenuItem::linkToUrl('Github Back', 'fab fa-github', 'https://github.com/O-clock-Meduse/projet-05-obaby-back')->setLinkTarget('_blank');
        yield MenuItem::linkToUrl('Github Front', 'fab fa-github', 'https://github.com/O-clock-Meduse/projet-05-obaby-front')->setLinkTarget('_blank');
        yield  MenuItem::linkToUrl('API Doc', 'fas fa-book', 'https://obaby.simschab.cloud/api')->setLinkTarget('_blank');
        yield  MenuItem::linkToUrl('Trello', 'fas fa-book', 'https://trello.com/b/1LfqByX4/obaby')->setLinkTarget('_blank');
        yield MenuItem::linkToUrl('Drive', 'fas fa-book', 'https://drive.google.com/drive/folders/1kvwJlH7UfDdVSF9iQqlUarCLX_pW0vMO')->setLinkTarget('_blank');

        // logout link
        yield MenuItem::section('Déconnexion',);
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}