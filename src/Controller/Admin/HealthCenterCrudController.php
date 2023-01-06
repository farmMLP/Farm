<?php

namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use App\Entity\Week;
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
            TextField::new('name'),
            TextField::new('address'),
            NumberField::new('phonenumber'),
            AssociationField::new('user'),
            AssociationField::new('shipmentDay')
        ];
    }
    
}
