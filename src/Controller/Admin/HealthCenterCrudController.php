<?php

namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\HealthCenter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HealthCenterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HealthCenter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
      return $crud->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
      return $actions
      ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
        return $action->setIcon('fa fa-plus')->addCssClass('btn btn-primary')->setLabel('Agregar nuevo Centro de Salud')->linkToCrudAction('new');
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
            // IdField::new('id'),
            TextField::new('name', 'Nombre del Centro de salud'),
            TextField::new('address', 'Dirección'),
            NumberField::new('phonenumber', 'Teléfono'),
            AssociationField::new('user', 'Usuario encargado del centro'),
            AssociationField::new('shipmentDay', 'Dia de entrega asociado al centro')
        ];
    }
    
}
