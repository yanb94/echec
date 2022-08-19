<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Legal;
use App\Entity\Message;
use App\Entity\Category;
use App\Controller\Admin\PostCrudController;
use App\Controller\Admin\LegalCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class AdminDashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(PostCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle("Le coin d'échec")
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Documents Légaux', 'fas fa-balance-scale', Legal::class);
        yield MenuItem::linkToCrud("Catégories", "fas fa-th-large", Category::class);
        yield MenuItem::linkToCrud("Forum", 'fas fa-comments', Post::class);
        yield MenuItem::linkToCrud("Messages", 'fas fa-envelope', Message::class);
    }
}
