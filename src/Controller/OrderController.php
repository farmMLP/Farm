<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Orders;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends AbstractController
{
    private $em;
    private $OrdersRepository;
    public function __construct(OrdersRepository $OrdersRepository, EntityManagerInterface $em)
    {
        $this->OrdersRepository = $OrdersRepository;
        $this->em = $em;
    }

    #[Route('/order', name: 'app_order')]
    public function index(OrdersRepository $OrdersRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $OrdersRepository->findAll(),
        ]);
    }
}
