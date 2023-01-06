<?php

namespace App\Controller\Admin;

use App\Entity\MedicalSamples;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class MedicalSamplesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MedicalSamples::class;
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
