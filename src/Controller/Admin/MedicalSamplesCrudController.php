<?php

namespace App\Controller\Admin;

use App\Entity\MedicalSamples;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class MedicalSamplesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MedicalSamples::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
      return $crud->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
      return $actions
      ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('healthCenter', 'Centro médico de la muestra médica'),
            AssociationField::new('product', 'Producto de la muestra'),
            NumberField::new('stock', 'Stock'),
            DateTimeField::new('expirationDate', 'Fecha de vencimiento'),
        ];
    }

}
