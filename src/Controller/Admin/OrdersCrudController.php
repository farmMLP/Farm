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
use App\Repository\ProductsByOrderRepository;
use App\Repository\MedicalSamplesRepository;

class OrdersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    private $em;

    public function __construct(EntityManagerInterface $em, OrdersRepository $orders, ProductsByOrderRepository $products, StatusRepository $status, MedicalSamplesRepository $medicalSamples, ProductsRepository $allProducts){
      $this->em= $em;
      $this->orders = $orders;
      $this->products = $products;
      $this->status = $status;
      $this->medicalSamples = $medicalSamples;
      $this->allProducts = $allProducts;
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
        // dd($context->getRequest()->request->all()['action']);
        if ($context->getRequest()->request->all()['action'] === "Autorizar") {
          if (isset($context->getRequest()->request->all()['quantity'])) {
            $catalog = $context->getRequest()->request->all()['contexto'];
            
            $productosGENERAL = $context->getRequest()->request->all()['productos'];
            // dd($productosGENERAL);
             // PRODUCTS ES PRODUCTOS POR PEDIDO SOLICITADO
            //PRODUCTOS GENERAL ES LOS PRODUCTOS QUE ESTOY MANDANDO, SI NO ESPECIFIQUE PRODUCTO VIENE VACIO, HAY QUE ESTABLECER COMPARACION CON PRODUCTOS POR PEDIDO Y MANEJAR
            // STOCKS CON RESPECTO AL PRODUCTO QUE YO ENVÍO, NO EL QUE ME SOLICITAN
              foreach ($context->getRequest()->request->all()['quantity'] as $key => $value)
              {
                if (!$value) {
                  $auxiliar= false;
                  break;              
                } else 
                {
                  // si se seleccionó el catálogo del programa o no se seleccionó ninguno (es decir, usó el input number default, se evalua el stock del programa)
                  if($catalog[$key] === 'Programa' || !$catalog[$key])
                  {
                    if (!$productosGENERAL[$key]) {
                      // ACÁ SE EVALUA EL STOCK DEL PRODUCTO SOLICITADO CON EL VALOR QUE YO LE ENVÍO, Y SU 
                      // PRODUCTOS GENERAL ES NULO, SIGNIFICA QUE EL VALOR Y EL STOCK QUE COMPARO ES CORRECTO
                      // PERO SI NO ES NULO, ES DECIR QUE SELECCIONÉ UN CATALOGO PROGRAMA Y CAMBIÉ EL PRODUCTO, TENGO QUE COMPARAR EL VALOR QUE ENVÍO
                      // CON EL STOCK DEL PRODUCOT INDICADO, NO EL PRODUCTO SOLICITADO.
                      if ($products[$key]->getProduct()->getStock() < $value){ 
                        // dd('ENTRE AL IF DEL PROGRAMA, PERO PUSE VALOR MÁS GRANDE QUE EL STOCK QUE POSEO DEL PROGRAMA');
                        $auxiliar = false;
                        break;
                        // return;
                        // mensaje de error al template
                      } else {
                        // dd('ENTRE AL IF DEL PROGRAMA, Y EL VALOR ES CORRECTO, SETEO LA CANTIDAD ENVIADA');
                        $products[$key]->setQuantitySent($value);
                      }
                    } else {
                      // $changedProduct = $this->allProducts->findOneById($productosGENERAL[$key]);
                      // $changedProduct->subStock($value);
                      if ($this->allProducts->findOneById($productosGENERAL[$key])->getStock() < $value){ 
                        // dd('ENTRE AL IF DEL PROGRAMA, PERO PUSE VALOR MÁS GRANDE QUE EL STOCK QUE POSEO DEL PROGRAMA');
                        $auxiliar = false;
                        break;
                        // return;
                        // mensaje de error al template
                      } else {
                        // dd('ENTRE AL IF DEL PROGRAMA, Y EL VALOR ES CORRECTO, SETEO LA CANTIDAD ENVIADA');
                        $products[$key]->setQuantitySent($value);
                      }
                      // dd('envié un producto que sí tiene input de catalogo y producto seleccionado, el cual es programa' CAMBIAR STOCK DEL NUEVO PRODUCTO, AHI TENGO EL ID);
                      // dd($productosGENERAL[$key]);
                      // $products[$key]->setQuantitySent($value);
                    }
                  } else 
                  {
                    // if (!$productosGENERAL[$key]) {
                    //   dd('ENCONTRE VALOR NULO EN INPUT ENVIADO DEL PRODUCTO POR MUESTRA MEDICA, TENGO QUE MANEJAR ID DE PRODUCTO POR PEDIDO');
                    // }
                    // dd($medicalSamples->findOneById($productosGENERAL[$key])->getStock());
                    if ($medicalSamples->findOneById($productosGENERAL[$key])->getStock() < $value){ 
                      
                      //  dd('ENTRE AL IF DE MUESTRA MÉDICA, PERO PUSE VALOR MÁS GRANDE QUE EL STOCK QUE POSEO DE MUESTRA MÉDICA');
                      $auxiliar = false;
                      break;
                      // return;
                      // mensaje de error al template
                    } else {
                      // dd('ENTRE AL IF DE MUESTRA MEDICA, Y EL VALOR ES CORRECTO, SETEO LA CANTIDAD ENVIADA');
                      $products[$key]->setQuantitySent($value);
                    }
                  }
                }
              }
  
            if ($auxiliar) {
              foreach ($context->getRequest()->request->all()['quantity'] as $key => $value){
                if($catalog[$key]=='Programa' || !$catalog[$key]){
                  if (!$productosGENERAL[$key]) {
                    // PRODUCTOS GENERAL NO POSEE VALOR, ES DECIR, SE SETEO UNA CANTIDAD ENVIADA DIRECTAMENTE SIN CAMBIAR PRODUCTO NI PROGRAMA, RESTO STOCK
                    $products[$key]->setQuantitySent($value);
                    $products[$key]->getProduct()->subStock($value);
                  } else {
                    // PRODUCTOS GENERAL EN KEY SÍ TIENE VALOR, ES DECIR, HUBO UNA SELECCIÓN DE INPUT Y ME ENCUENTRO EN PROGRAMA
                    $changedProduct = $this->allProducts->findOneById($productosGENERAL[$key]);
                    $changedProduct->subStock($value);
                    $this->allProducts->save($changedProduct,true);
                  }
                  $this->products->save($products[$key], true);
                } else {
                  $medicalSample=$medicalSamples->findOneById($productosGENERAL[$key]);
                  $medicalSample->subStock($value);
                  $medicalSamples->save($medicalSample,true);
                }
              }
              // seteo estado de aprobado (2)
              $order->setStatus($this->status->findOneById(2));
              $this->orders->save($order, true);
              return $this->redirect('admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5COrdersCrudController');
            } else {
              return $this->render('admin/showOrder.html.twig', [
                "order" => $order,
                "products" => $products,
                "error" => $auxiliar,
                "medicalSamples" => $medicalSamples,
                "allProducts" => $this->allProducts
              ]);
            }
          }        
        } else {
          // seteo valor rechazado (3)
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
        "allProducts" => $this->allProducts
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