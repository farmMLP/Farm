<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Products;
use App\Entity\Orders;
use App\Entity\Status;
use App\Entity\HealthCenter;
use App\Entity\ProductsByOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{
        private $em;


    public function __construct(
        EntityManagerInterface $em,
       
    ) {
     
        $this->em         = $em;
     
    }
    #[Route('/', name: 'app_user')]
    public function index(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
               return $this->redirectToRoute('admin');
        }   
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    //     #[Route('/pedido', name: 'app_pedido')]
    // public function pedido(Request $request,ManagerRegistry $doctrine): Response
    // {

    //     if (isset($request->request->all()['producto'])) {

    //         $order = new Orders();
    //         $order->setCreatedAt(new \DateTimeImmutable);
    //         $order->setUser($this->getUser());
    //         $order->setMemo('no se que es');
    //         $order->setStatus($doctrine->getRepository(Status::class)->findOneById(1));
    //         $order->setHealthCenter($doctrine->getRepository(HealthCenter::class)->findOneById(1));
    //         $this->em->persist($order);
    //         $this->em->flush();

    //         foreach ($request->request->all()['producto'] as $key => $value) {
    //             if ($request->request->all()['cantidad'][$key]!='') {
                
    //             $productsByOrder= new ProductsByOrder();
    //             $productsByOrder->setProduct($doctrine->getRepository(Products::class)->findOneById($value));
    //             $productsByOrder->setQuantityRequested($request->request->all()['cantidad'][$key]);
    //             $productsByOrder->setPetition($order);
    //             $this->em->persist($productsByOrder);
    //             }
    //         }
    //         $this->em->flush();


    //         }
           

    //     $productos = $doctrine->getRepository(Products::class)->findAll();

    //     return $this->render('user/pedido.html.twig', [
    //         'productos' => $productos,
    //     ]);
    // }


}
