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
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
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
    
  #[Route('/pedidos/filtrar/{id}', name: 'app_ordered_list')]
  public function getFilteredOrders($id, Security $sec){
    // $ordersMicentro= $this->OrdersRepository->findByHealthCenter($sec->getUser()->getHealthCenter());
    // dd($this->OrdersRepository);
    $orders= $this->OrdersRepository->findByStatusAndHealthCenter($id,$sec->getUser()->getHealthCenter());
    $response = array();

    foreach($orders as $order){
      $response[] = array(
        'id' => $order->getId(),
        'created_at' => $order->getCreatedAt(),
        'memo' => $order->getMemo(),
        'user' => $order->getUser()->getLastname(),
        'status' => $order->getStatus()->getDescription()
      );
    }
    
    return new JsonResponse($response);
  }

    #[Route('/pedidos', name: 'app_order')]
    public function index(OrdersRepository $OrdersRepository , Security $security, PaginatorInterface $paginator, Request $request): Response
    {
      $pagination = $paginator->paginate(
        $OrdersRepository->paginationQuery($security->getUser()->getHealthCenter()),
        $request->query->get('page', 1),
        1
      );
      return $this->render('order/index.html.twig', [
        'pagination' => $pagination
    ]);
        // $orders = $OrdersRepository->findByHealthCenter($security->getUser()->getHealthCenter());
        // return $this->render('order/index.html.twig', [
        //     'orders' => $orders
        // ]);
    }

    #[Route('/pedidos/nuevo', name: 'appOrder_new', methods: ['GET' , 'POST'])]
    public function main(Request $request, ManagerRegistry $doctrine, Security $sec): Response
    {
        if (isset($request->request->all()['producto'])) {
            // dd($request);
            $order = new Orders();
            $order->setCreatedAt(new \DateTimeImmutable);
            $order->setUser($this->getUser());
            $sanitizedMemo = strip_tags($request->request->get('memo'));
            $order->setMemo($sanitizedMemo);
            $order->setStatus($doctrine->getRepository(Status::class)->findOneById(1));
            $order->setHealthCenter($sec->getUser()->getHealthCenter());
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
