<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
      return $crud->showEntityActionsInlined()
      ->setPageTitle('index','Productos')
      ->setPageTitle('edit','Editar producto')
      ->setPageTitle('detail','Producto');
    }

    public function configureActions(Actions $actions): Actions
    {
      return $actions
      ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
        return $action->setIcon('fa fa-plus')->addCssClass('btn btn-primary')->setLabel('Agregar un producto')->linkToCrudAction('new');
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
            TextField::new('name', 'Nombre del producto'),
            TextField::new('description', 'Descripción'),
            NumberField::new('stock', 'Stock')
        ];
    }
}
