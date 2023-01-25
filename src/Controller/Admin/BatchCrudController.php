<?php

namespace App\Controller\Admin;

use App\Entity\Batch;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Repository\ProductsRepository;
use App\Repository\BatchRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

use Symfony\Component\HttpFoundation\Response;

class BatchCrudController extends AbstractCrudController
{

    public function __construct(EntityManagerInterface $em, UserRepository $users, ProductsRepository $products, BatchRepository $batchs){
      $this->em= $em;
      $this->users = $users;
      $this->products = $products;
      $this->batchs = $batchs;
    }

    public static function getEntityFqcn(): string
    {
        return Batch::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
      return $crud->showEntityActionsInlined()
      ->setPageTitle('index','Lotes')
      ->setPageTitle('edit','Editar Lote')
      ->setPageTitle('detail', 'Lote');
    }

    public function configureActions(Actions $actions): Actions
    {
      return $actions
      ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
        return $action->setIcon('fa fa-plus')->addCssClass('btn btn-primary')->setLabel('Agregar Lote')->linkToCrudAction('new');
      })
      ->add(Crud::PAGE_INDEX, Action::DETAIL)
      ->update(Crud::PAGE_INDEX,Action::DETAIL,function(Action $action){
        return $action->setIcon('fa fa-eye')->addCssClass('btn btn-info')->setLabel('Ver');
        })
      ->update(Crud::PAGE_INDEX,Action::EDIT,function(Action $action){
        return $action->setIcon('fa fa-edit')->addCssClass('btn btn-success')->setLabel('Editar');
        })
      ->update(Crud::PAGE_INDEX,Action::DELETE,function(Action $action){
        return $action->setIcon('fa fa-trash')->addCssClass('btn btn-danger text-white')->setLabel('Eliminar');
      })
      ->update(Crud::PAGE_DETAIL,Action::DELETE,function(Action $action){
        return $action->setIcon('fa fa-trash')->addCssClass('btn btn-danger text-white')->setLabel('Eliminar');
      })
      ->update(Crud::PAGE_DETAIL,Action::INDEX,function(Action $action){
        return $action->setIcon('fa fa-eye')->setLabel('Volver');
      })
      ->update(Crud::PAGE_DETAIL,Action::EDIT,function(Action $action){
        return $action->setIcon('fa fa-edit')->setLabel('Editar')->addCssClass('btn btn-success');
      })
      ->update(Crud::PAGE_EDIT,Action::SAVE_AND_RETURN,function(Action $action){
        return $action->setIcon('fa fa-edit')->setLabel('Guardar')->addCssClass('btn btn-secondary');
      })
      ->update(Crud::PAGE_EDIT,Action::SAVE_AND_CONTINUE,function(Action $action){
        return $action->setIcon('fa fa-edit')->setLabel('Guardar y seguir editando')->addCssClass('btn btn-secondary');
      });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user', 'Usuario'),
            AssociationField::new('product', 'Producto'),
            NumberField::new('quantity', 'Cantidad'),
            DateTimeField::new('createdAt', 'Fecha de ingreso'),
            DateTimeField::new('expirationDate', 'Fecha de vencimiento'),
            TextField::new('code', 'Código de lote'),
        ];
    }
  
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add('user')
            ->add(EntityFilter::new('product'))
            ->add('expirationDate')
        ;
    }

    public function new(AdminContext $context): Response 
    {
      if ($context->getRequest()->isMethod('POST')){

        $user = $this->users->findOneById($context->getRequest()->request->get('user'));
        $product = $this->products->findOneById($context->getRequest()->request->get('product'));

        $newBatch = new Batch();
        $newBatch->setUser($user);
        $newBatch->setProduct($product);
        $newBatch->setQuantity($context->getRequest()->request->get('cantidad'));
        $newBatch->setCreatedAt(new \DateTimeImmutable);
        $newBatch->setCode($context->getRequest()->request->get('batchCode'));
        $newBatch->setExpirationDate(new \DateTimeImmutable($context->getRequest()->request->get('fechaVencimiento')));

        $product->addStock($context->getRequest()->request->get('cantidad'));

        $this->products->save($product, true);
        $this->batchs->save($newBatch, true);
      }
      return $this->render('admin/createBatch.html.twig',[
      "users" => $this->users->findAll(),
      "products" => $this->products->findAll()
      ]);
    }

    public function edit(AdminContext $context): Response 
    {
      // $users = $this->users->getAll();
      // $aux = $context->getRequest()->query->get('crudAction');
      // if ($aux == 'edit') {
      //   dd('EDITANDO');
      // } else {
      //   dd('CREANDO');
      // }
      $batchId = $context->getRequest()->query->get('entityId');
      $updatedBatch = $this->batchs->findOneById($batchId);
      
      if ($context->getRequest()->isMethod('POST')){

        
        $user = $this->users->findOneById($context->getRequest()->request->get('user'));
        $product = $this->products->findOneById($context->getRequest()->request->get('product'));

        
        // $newBatch = new Batch();
        $updatedBatch->setUser($user);
        $updatedBatch->setProduct($product);

        $oldQuantity = $updatedBatch->getQuantity();
        $updatedBatch->setQuantity($context->getRequest()->request->get('cantidad'));
        $updatedBatch->setCreatedAt(new \DateTimeImmutable);
        $updatedBatch->setCode($context->getRequest()->request->get('batchCode'));
        $updatedBatch->setExpirationDate(new \DateTimeImmutable($context->getRequest()->request->get('fechaVencimiento')));

        if ($oldQuantity >= $context->getRequest()->request->get('cantidad'))
        {
          $newQuantity = $oldQuantity - $context->getRequest()->request->get('cantidad');
          $product->subStock($newQuantity);
          $this->products->save($product, true);
        } else 
        {
          $newQuantity = $context->getRequest()->request->get('cantidad') - $oldQuantity;
          $product->addStock($newQuantity);
          $this->products->save($product, true);
        }
        // $this->products->save($product, true);
        $this->batchs->save($updatedBatch, true);
      }
      return $this->render('admin/editBatch.html.twig',[
      "users" => $this->users->findAll(),
      "products" => $this->products->findAll(),
      "batchValues" => $updatedBatch
      ]);
    }
}