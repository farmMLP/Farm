<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Entity\Orders;
use App\Entity\Status;
use App\Entity\HealthCenter;
use App\Entity\ProductsByOrder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

class OrdersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    private $em;

    public function __construct(EntityManagerInterface $em){
      $this->em= $em;
    }

    public function configureCrud(Crud $crud): Crud
    {
      return $crud->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
      return $actions
      // ->add(Crud::PAGE_INDEX, 'carlitos')
      // ->update(Crud::PAGE_INDEX,'carlitos',function(Action $action){
      ->add(Crud::PAGE_INDEX, Action::DETAIL)
      ->update(Crud::PAGE_INDEX,Action::DETAIL,function(Action $action){
        return $action->setIcon('fa fa-eye')->addCssClass('btn btn-info');
        })
      ->update(Crud::PAGE_INDEX,Action::EDIT,function(Action $action){
        return $action->setIcon('fa fa-edit')->addCssClass('btn btn-success');
        })
      ->update(Crud::PAGE_INDEX,Action::DELETE,function(Action $action){
        return $action->setIcon('fa fa-trash')->addCssClass('btn btn-danger text-white');
      });
    }

    public function detail(AdminContext $context) : Response {
      return $this->render('admin/showOrder.html.twig');
    }

    // public function new(AdminContext $context, Request $request, ManagerRegistry $doctrine): Response
    // {
    //     if (isset($request->request->all()['producto'])) {
    //       // dd($request);
    //         $order = new Orders();
    //         $order->setCreatedAt(new \DateTimeImmutable);
    //         $order->setUser($this->getUser());
    //         $order->setMemo('no se que es');
    //         $order->setStatus($doctrine->getRepository(Status::class)->findOneById(1));
            
    //         $order->setHealthCenter($doctrine->getRepository(HealthCenter::class)->findOneById($request->request->get('healthCenter')));
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
    //     $healthCenters = $doctrine->getRepository(HealthCenter::class)->findAll();

    //     return $this->render('admin/createOrder.html.twig', [
    //         'productos' => $productos,
    //         'healthCenters' => $healthCenters
    //     ]);
    // }

    public function main(AdminContext $context, Request $request, ManagerRegistry $doctrine): Response
    {
        if (isset($request->request->all()['producto'])) {
          // dd($request);
            $order = new Orders();
            $order->setCreatedAt(new \DateTimeImmutable);
            $order->setUser($this->getUser());
            $order->setMemo('no se que es');
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