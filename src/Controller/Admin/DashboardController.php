<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Products;
use App\Entity\HealthCenter;
use App\Entity\MedicalSamples;
use App\Entity\Batch;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


class DashboardController extends AbstractDashboardController


{

    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ){}

    

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        $url = $this->adminUrlGenerator
        ->setController(UserCrudController::class)
        ->generateUrl();

        return $this->redirect($url);




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
            ->setTitle('Farm');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);

        yield MenuItem::section('Productos');
        yield MenuItem::subMenu('Acciones', 'fas fa-bars')->setSubItems([
            //   MenuItem::linkToCrud('Create product', 'fas fa-plus', ProductoCrudController::class)->setAction(Crud::PAGE_NEW),
              MenuItem::linkToCrud('Ver productos', 'fas fa-eye', Products::class),
              MenuItem::linkToCrud('Crear producto', 'fas fa-plus', Products::class)->setAction(Crud::PAGE_NEW)
            ]);

        yield MenuItem::section('Centros de salud');
        yield MenuItem::subMenu('Acciones', 'fas fa-bars')->setSubItems([
            //   MenuItem::linkToCrud('Create product', 'fas fa-plus', ProductoCrudController::class)->setAction(Crud::PAGE_NEW),
              MenuItem::linkToCrud('Ver centros', 'fas fa-eye', HealthCenter::class),
              MenuItem::linkToCrud('Crear centros', 'fas fa-plus', HealthCenter::class)->setAction(Crud::PAGE_NEW)
            ]);
        
        yield MenuItem::section('Lotes');
        yield MenuItem::subMenu('Acciones', 'fas fa-bars')->setSubItems([
            //   MenuItem::linkToCrud('Create product', 'fas fa-plus', ProductoCrudController::class)->setAction(Crud::PAGE_NEW),
              MenuItem::linkToCrud('Ver Lotes', 'fas fa-eye', Batch::class),
              MenuItem::linkToCrud('Crear Lotes', 'fas fa-plus', Batch::class)->setAction(Crud::PAGE_NEW)
            ]);
        yield MenuItem::section('Muestras médicas');
        yield MenuItem::subMenu('Acciones', 'fas fa-bars')->setSubItems([
            //   MenuItem::linkToCrud('Create product', 'fas fa-plus', ProductoCrudController::class)->setAction(Crud::PAGE_NEW),
              MenuItem::linkToCrud('Ver muestras médicas', 'fas fa-eye', MedicalSamples::class),
              MenuItem::linkToCrud('Crear muestras médicas', 'fas fa-plus', MedicalSamples::class)->setAction(Crud::PAGE_NEW)
            ]); 
    }
}
