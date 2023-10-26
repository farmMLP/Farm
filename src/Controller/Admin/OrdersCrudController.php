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
use App\Repository\BatchRepository;
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

    public function __construct(EntityManagerInterface $em, OrdersRepository $orders, ProductsByOrderRepository $products, StatusRepository $status, MedicalSamplesRepository $medicalSamples, ProductsRepository $allProducts, BatchRepository $allBatchs){
      $this->em= $em;
      $this->orders = $orders;
      $this->products = $products;
      $this->status = $status;
      $this->medicalSamples = $medicalSamples;
      $this->allProducts = $allProducts;
      $this->allBatchs = $allBatchs;
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
            //TextField::new('memo', 'Memo'),
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
      $auxiliar=true;
      $medicalSamples = $this->medicalSamples;
      
      if ($context->getRequest()->isMethod('POST')){
        // dd($context->getRequest()->request->all());
        // veo qué botón se clickeó, si se autorizó o rechazó
        if ($context->getRequest()->request->all()['action'] === "Autorizar") {
          // evalúo que todas las cantidades estén seteadas
          if (isset($context->getRequest()->request->all()['quantity'])) {
            // veo todos los contextos (catálogos) se eligieron en el formulario
            $catalog = $context->getRequest()->request->all()['contexto'];
            // veo todos los productos que se setearon (al haber cambio de producto, aquellos inputs que se dejaron default van a estar 'vacios')
            $batchsSelected = $context->getRequest()->request->all()['productos'];
              // por cada input de cantidad (es el que tengo asegurado de tener lleno y con valores seteados)
              foreach ($context->getRequest()->request->all()['quantity'] as $key => $value)
              {
                // si el valor es nulo, corto y envío mensaje de error ($auxiliar)
                if ($value == null || $value < 0) {
                  $auxiliar= false;
                  break;              
                } else 
                {
                  // si se seleccionó el catálogo del programa
                  if($catalog[$key] === 'Programa')
                  {
                      // si se seleccionó un lote por parte del programa, evalúo stock
                      if ($this->allBatchs->findOneById($batchsSelected[$key])->getQuantity() < $value){ 
                        // si el stock que se posee del lote que yo cambié es menor al valor que se seteó, corto y envío mensaje de error ($auxiliar)
                        $auxiliar = false;
                        break;
                      } else {
                        // seteo cantidad enviada en caso de que el valor esté bien
                        $products[$key]->setQuantitySent($value);
                      }
                    }
                  else 
                  // si el catálogo elegido no es programa ni nulo, entonces es muestra médica
                  {
                    // si se seleccionó un producto de una muestra médica, evalúo stock
                    if ($medicalSamples->findOneById($batchsSelected[$key])->getStock() < $value){ 
                      // si el stock que se posee del producto elegido de la muestra médica es menor al valor que se seteó, corto y envío mensaje de error ($auxiliar)
                      $auxiliar = false;
                      break;
                    } else {
                      // seteo cantidad enviada en caso de que el valor esté bien
                      $products[$key]->setQuantitySent($value);
                    }
                  }
                }
              }
            // si las cantidades estaban correctas
            if ($auxiliar) {
              foreach ($context->getRequest()->request->all()['quantity'] as $key => $value){
                // según sea catalogo: Programa o muestra médica o nulo, cambio los stocks
                if($catalog[$key]=='Programa'){
                    // seteo stocks en el producto y lote que yo cambié
                    $batch = $this->allBatchs->findOneById($batchsSelected[$key]);
                    $product = $batch->getProduct();
                    $batch->subQuantity($value);
                    $product->subStock($value);
                    $this->allBatchs->save($batch,true);
                    $this->allProducts->save($product, true);
                } else {
                  // seteo stocks en la muestra médica que yo indiqué
                  $medicalSample=$medicalSamples->findOneById($batchsSelected[$key]);
                  $medicalSample->subStock($value);
                  $medicalSamples->save($medicalSample,true);
                }
              }
              // seteo estado de aprobado (2)
              // concateno strings para el memo
              $oldMemo = $order->getMemo();
              $updatedMemo = $context->getRequest()->request->get('memo');
              $order->setMemo($oldMemo. "\r\n" . "RTA: " .  "\r\n" . $updatedMemo);
              $order->setStatus($this->status->findOneById(2));
              $this->orders->save($order, true);
              return $this->redirect('admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5COrdersCrudController');
            } else {
              return $this->render('admin/showOrder.html.twig', [
                "order" => $order,
                "products" => $products,
                "error" => $auxiliar,
                "medicalSamples" => $medicalSamples,
                "allBatchs" => $this->allBatchs
              ]);
            }
          }        
        } else {

          
          // seteo valor rechazado (3)
          // concateno strings para el memo
          $oldMemo = $order->getMemo();
          $updatedMemo = $context->getRequest()->request->get('memo');
          $order->setMemo($oldMemo." --- ". " \r\n ".$updatedMemo);
          $order->setStatus($this->status->findOneById(3));
          $this->orders->save($order, true);
          return $this->redirect('admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5COrdersCrudController');
        }
      }
      
      return $this->render('admin/showOrder.html.twig', [
        "order" => $order,
        "products" => $products,
        "error" => $auxiliar,
        "medicalSamples" => $medicalSamples,
        "allBatchs" => $this->allBatchs
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