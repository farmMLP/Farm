<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Entity\Orders;
use App\Entity\Status;
use App\Repository\StatusRepository;
use App\Repository\ProductsRepository;
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
use App\Repository\BatchRepository;
use App\Repository\ProductsByOrderRepository;
use App\Repository\MedicalSamplesRepository;

class OrdersCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    private $em;

    public function __construct(EntityManagerInterface $em, OrdersRepository $orders, ProductsByOrderRepository $products, StatusRepository $status, MedicalSamplesRepository $medicalSamples, ProductsRepository $allProducts, BatchRepository $allBatchs){
      $this->em= $em;
      $this->orders = $orders;
      $this->products = $products;
      $this->status = $status;
      $this->medicalSamples = $medicalSamples;
      $this->allProducts = $allProducts;
      $this->allBatchs = $allBatchs;
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
            DateTimeField::new('createdAt', 'Fecha de solicitud')->setTimezone('America/Argentina/Buenos_Aires'),
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

      // ->add(Crud::PAGE_INDEX, Action::ACTION_PRINT)
      // ->update(Crud::PAGE_INDEX,Action::ACTION_PRINT,function(Action $action){
      //   return $action->setIcon('fa-solid fa-print')->addCssClass('btn btn-success')->setLabel('Imprimir'); 
      // })  

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
      // no tengo que enviar un array vacio por defecto a la página, tal vez conviene resetear el valor post submit.
      $auxiliar=[];
      $valuesAreOK=true;
      
      if ($context->getRequest()->isMethod('POST')){
        // veo qué botón se clickeó, si se autorizó o rechazó
        if ($context->getRequest()->request->all()['action'] === "Autorizar") {
          $productsSelected = $context->getRequest()->request->all()['cantproductos'];
          
          // FOR PARA EVALUAR CANTIDADES ANTES DE HACER ALGO.

          foreach ($productsSelected as $key => $value){
            if(array_key_exists('batchs', $value)){
              $batchs = $value['batchs'];
              $quantitys = $value['quantitys'];
              foreach ($batchs as $keyBatch => $idBatch){
                $selectedBatch = $this->allBatchs->findOneById($idBatch);
                if(!($quantitys[$keyBatch] <= $selectedBatch->getQuantity())){
                  $valuesAreOK = false;
                  return $this->render('admin/showOrder.html.twig', [
                    "order" => $order,
                    "products" => $products,
                    "error" => $valuesAreOK,
                    "medicalSamples" => $this->medicalSamples,
                    "allProducts" => $this->allProducts,
                    "allBatchs" => $this->allBatchs->findAll()
                  ]);
                }
              }
              $products[$key]->setQuantitySent($value['totalQuantity'][0]);
              $this->products->save($products[0], true);
            } else { 
              // value[] es la muestra medica seleccionada con su stock actual.
              // value['muestramedica'] tiene el id de la muestra seleccionada.
              // value['quantity'] es el stock que tiene la muestra medica seleccionada.
              $medicalSample = $this->medicalSamples->findOneById($value['muestramedica']);
              // EVALUO STOCK Y MANTENGO AUXILIAR
              if (!($value['quantity'] <= $medicalSample->getStock())){
                  // array_push($auxiliar, "invalido");
                  $valuesAreOK = false;
                  return $this->render('admin/showOrder.html.twig', [
                    "order" => $order,
                    "products" => $products,
                    "error" => $valuesAreOK,
                    "medicalSamples" => $this->medicalSamples,
                    "allProducts" => $this->allProducts,
                    "allBatchs" => $this->allBatchs->findAll()
                  ]);
              }
            }
          }
          // FOR PARA ACTUALIZAR VALORES EN CASO DE QUE LO VALORES SEAN CORRECTOS.
            foreach ($productsSelected as $key => $value){
              if(array_key_exists('batchs', $value)){
                // value[] puede poseer un array de lotes y cantidades.
                $batchs = $value['batchs'];
                $quantitys = $value['quantitys'];
                foreach ($batchs as $keyBatch => $idBatch){
                  $selectedBatch = $this->allBatchs->findOneById($idBatch);
                  $product = $selectedBatch->getProduct();
                  if($quantitys[$keyBatch] <= $selectedBatch->getQuantity()){
                    $selectedBatch->subQuantity($quantitys[$keyBatch]);
                    $product->subStock($quantitys[$keyBatch]);
                    $this->allProducts->save($product, true);
                    $this->allBatchs->save($selectedBatch,true);
                  }
                }
                $products[$key]->setQuantitySent($value['totalQuantity'][0]);
                $this->products->save($products[$key], true);
              } else {
                // value[] es la muestra medica seleccionada con su stock actual.
                // value['muestramedica'] tiene el id de la muestra seleccionada.
                // value['quantity'] es el stock que tiene la muestra medica seleccionada.
                $medicalSample = $this->medicalSamples->findOneById($value['muestramedica']);
                $medicalSample->subStock($value['quantity']);
                $this->medicalSamples->save($medicalSample, true);
                $products[$key]->setQuantitySent($value['quantity']);
                $this->products->save($products[$key], true);
              }
            }
            $oldMemo = $order->getMemo();
            $updatedMemo = $context->getRequest()->request->get('memo');
            $order->setMemo($oldMemo. "\r\n" . "RTA: " . $updatedMemo);
            $order->setStatus($this->status->findOneById(2));
            $this->orders->save($order, true);
            return $this->redirect('admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5COrdersCrudController');
        } else if ($context->getRequest()->request->all()['action'] === "Rechazar") {
          // pedido rechazado
          $oldMemo = $order->getMemo();
          $updatedMemo = $context->getRequest()->request->get('memo');
          $order->setMemo($oldMemo. "\r\n" . "RTA: " .$updatedMemo);
          $order->setStatus($this->status->findOneById(3));
          $this->orders->save($order, true);
          return $this->redirect('admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5COrdersCrudController');
        }  
      }
      return $this->render('admin/showOrder.html.twig', [
        "order" => $order,
        "products" => $products,
        "error" => $valuesAreOK,
        "medicalSamples" => $this->medicalSamples,
        "allProducts" => $this->allProducts,
        "allBatchs" => $this->allBatchs->findAll()
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

    public function generatePdfById($id, OrdersRepository $ordersRepository, productsByOrderRepository $productsByOrderRepository, PdfService $pdf)
    {
        // Get the product from the database
        $order = $ordersRepository->findOneById($id);
        $productos = $productsByOrderRepository->findByPetition($id);
        
      $html = $this->renderView('order/pdf.html.twig', [
        'products' => $productos,
        'order' => $order,
      ]); 
       
    return new Response($pdf->showPdfFile($html),200,array('Content-Type'=>'application/pdf'))  ;
      
    }
}