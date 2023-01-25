<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Entity\Orders;
use App\Entity\Status;
use App\Repository\StatusRepository;
use App\Entity\HealthCenter;
use App\Entity\ProductsByOrder;


use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use App\Repository\OrdersRepository;
use App\Repository\ProductsByOrderRepository;
use App\Repository\MedicalSamplesRepository;

class OrdersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    private $em;

    public function __construct(EntityManagerInterface $em, OrdersRepository $orders, ProductsByOrderRepository $products, StatusRepository $status, MedicalSamplesRepository $medicalSamples){
      $this->em= $em;
      $this->orders = $orders;
      $this->products = $products;
      $this->status = $status;
      $this->medicalSamples = $medicalSamples;
      // $this->request = $request;
    }

    public function configureCrud(Crud $crud): Crud
    {
      return $crud->showEntityActionsInlined()
      ->setPageTitle('index','Pedidos');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user', 'Usuario')->hideOnForm(),
            DateTimeField::new('createdAt', 'Fecha de solicitud'),
            TextField::new('memo', 'Memo'),
            TextField::new('healthCenter', 'Centro de salud'),
            AssociationField::new('status','Estado')
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('memo')
            ->add('user')
            ->add(EntityFilter::new('status'))
            ->add('healthCenter')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
      return $actions
      ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
        return $action->setIcon('fa fa-plus')->addCssClass('btn btn-primary')->setLabel('Solicitar nuevo pedido')->linkToCrudAction('main');
      })
      ->add(Crud::PAGE_INDEX, Action::DETAIL)
      ->update(Crud::PAGE_INDEX,Action::DETAIL,function(Action $action){
        return $action->setIcon('fa fa-eye')->addCssClass('btn btn-info')->setLabel('Ver');
        })
      ->disable(Action::EDIT)
      ->update(Crud::PAGE_INDEX,Action::DELETE,function(Action $action){
        return $action->setIcon('fa fa-trash')->addCssClass('btn btn-danger text-white')->setLabel('Eliminar');
      });
    }

    public function detail(AdminContext $context): Response 
    {
      $orderId = $context->getRequest()->query->get('entityId');
      $order = $this->orders->findOneById($orderId);
      $products = $this->products->findByPetition($orderId);
      $auxiliar=true;
      $medicalSamples = $this->medicalSamples;
      
      if ($context->getRequest()->isMethod('POST')){
        // dd($context->getRequest()->request->all());
        //if ((isset($context->getRequest()->request->all()['quantity'])) || (isset($context->getRequest()->request->all()['medicalQuantitys'])) ){
        if (isset($context->getRequest()->request->all()['quantity'])) {
          // while ($auxiliar = true) {
            foreach ($context->getRequest()->request->all()['quantity'] as $key => $value){
              if (!$value) {
                $auxiliar= false;
              } else {
                if ($products[$key]->getProduct()->getStock() < $value){ 
                  // EVALUO SI LA CANTIDAD QUE ENVIO ES MENOR A LA DE STOCK Y MANTENGO UNA VARIABLE AUXILIAR
                  $auxiliar = false;
                  break;
                  // return;
                  // mensaje de error al template
                } else {
                  $products[$key]->setQuantitySent($value);
                }
              }
              // if (!$auxiliar) {
              //   ret
              // }
              // SI UNA CANTIDAD SE ENVIA MAL, SIGUE EN EL FOR, DEBERIA ROMPER POR COMPLETO Y RETORNAR ERROR.
              // if ($products[$key]->getProduct()->getStock() < $value){ 
              //   dd('MANEJO DE ERROR DE STOCK');
              // }
              // else {
              //   $products[$key]->setQuantitySent($value);
              //   $this->products->save($products[$key], true);
              // }
            }
          // }
          // dd($products[$key]);

          if ($auxiliar) {
            foreach ($context->getRequest()->request->all()['quantity'] as $key => $value){
              $products[$key]->getProduct()->subStock($value);
              $this->products->save($products[$key], true);
            }
            $order->setStatus($this->status->findOneById(2));
            $this->orders->save($order, true);
            return $this->redirect('admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5COrdersCrudController');
          } else {
            return $this->render('admin/showOrder.html.twig', [
              "order" => $order,
              "products" => $products,
              "error" => $auxiliar,
              "medicalSamples" => $medicalSamples
            ]);
          }
        }
      }
      
      // foreach ($products as $key => $product){
      //   dd($product);
      // }
      // dd($medicalSamples->findOneByProduct($products["0"]->getProduct()));
      // dd($medicalSamples);
      return $this->render('admin/showOrder.html.twig', [
        "order" => $order,
        "products" => $products,
        "error" => $auxiliar,
        "medicalSamples" => $medicalSamples
      ]);
    }
    
    public function main(AdminContext $context, Request $request, ManagerRegistry $doctrine): Response
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

        return $this->render('admin/createOrder.html.twig', [
            'productos' => $productos,
            'healthCenters' => $healthCenters
        ]);
    }
}