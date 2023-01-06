<?php

namespace App\Controller\Admin;

use App\Entity\Batch;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Repository\ProductsRepository;

class BatchCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Batch::class;
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
}