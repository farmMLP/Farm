<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    // agregar fecha de actualizacion a lote, mantener stock original y ver cuanto queda en campos diferentes para tener mejor integridad de info
    // ver tema tablas pedidos. tengo que enviar ubn lote, pero posiblemente tenga que enviar más de un lote por producto en pedido. implica nuevas relaciones, nuevas tablas.
}
