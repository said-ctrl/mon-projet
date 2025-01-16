<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Identifiant;
use App\Entity\Order;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

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
            ->setTitle('Mon Site');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Articles', 'fa fa-newspaper', Article::class)
            ->setController(ArticleCrudController::class); // Utilisation du contrôleur CRUD pour Article
        yield MenuItem::linkToCrud('Identifiants', 'fa fa-user', Identifiant::class)
            ->setController(IdentifiantCrudController::class); // Utilisation du contrôleur CRUD pour Identifiant
         yield MenuItem::linkToCrud('Product', 'fa fa-newspaper', Product::class);
         yield MenuItem::linkToCrud('Order', 'fa fa-newspaper', Order::class);

         yield MenuItem::linkToUrl('Retour à l\'accueil', 'fa fa-home', $this->generateUrl('home'));
    }
}
