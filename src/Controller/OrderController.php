<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Orders;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Entity\Status;
use App\Entity\HealthCenter;
use App\Entity\ProductsByOrder;
use App\Entity\Products;
use Symfony\Component\Security\Core\Security;
use App\Repository\ProductsByOrderRepository;


use Doctrine\Persistence\ManagerRegistry;

class OrderController extends AbstractController
{
    private $em;
    private $OrdersRepository;
    public function __construct(OrdersRepository $OrdersRepository, EntityManagerInterface $em)
    {
        $this->OrdersRepository = $OrdersRepository;
        $this->em = $em;
    }

    #[Route('/pedidos', name: 'app_order')]
    public function index(OrdersRepository $OrdersRepository , Security $security): Response
    {
        $orders = $OrdersRepository->findByHealthCenter($security->getUser()->getHealthCenter());
        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/pedidos/nuevo', name: 'appOrder_new', methods: ['GET' , 'POST'])]
    public function main(Request $request, ManagerRegistry $doctrine): Response
    {
        if (isset($request->request->all()['producto'])) {
            // dd($request);
            $order = new Orders();
            $order->setCreatedAt(new \DateTimeImmutable);
            $order->setUser($this->getUser());
            $order->setMemo($request->request->get('memo'));
            $order->setStatus($doctrine->getRepository(Status::class)->findOneById(1));
            $order->setHealthCenter($doctrine->getRepository(HealthCenter::class)->findOneById($request->request->get('healthCenter')));
            $this->em->persist($order);
            $this->em->flush();

            foreach ($request->request->all()['producto'] as $key => $value) {
                if ($request->request->all()['cantidad'][$key]!='') {
                
                $productsByOrder= new ProductsByOrder();
                $productsByOrder->setProduct($doctrine->getRepository(Products::class)->findOneById($value));
                $productsByOrder->setQuantityRequested($request->request->all()['cantidad'][$key]);
                $productsByOrder->setPetition($order);
                $this->em->persist($productsByOrder);
                }
            }
            $this->em->flush();


            }
           
        $productos = $doctrine->getRepository(Products::class)->findAll();
        $healthCenters = $doctrine->getRepository(HealthCenter::class)->findAll();

        return $this->render('order/createOrder.html.twig', [
            'productos' => $productos,
            'healthCenters' => $healthCenters
        ]);
    }

    #[Route('/pedidos/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Request $request, OrdersRepository $ordersRepository, ProductsByOrderRepository $productsByOrderRepository, Security $sec, $id): Response
    {
       $data=$productsByOrderRepository->findByPetition($id);
       $order=$ordersRepository->findOneById($id);

       if($order){

        if($order->getHealthCenter() == $sec->getUser()->getHealthCenter()){
            return $this->render('order/showOrderUser.html.twig', [
                'products'=>$data,
                'order'=>$order     
            ]);

        }
            else{
                return $this->render('order/error.html.twig'); 
            }

    }
    else{
        return $this->render('order/error.html.twig'); 
    }
}
}
