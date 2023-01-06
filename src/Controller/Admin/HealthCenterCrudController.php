<?php

namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
