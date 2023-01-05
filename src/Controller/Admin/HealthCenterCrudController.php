<?php

namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use App\Entity\User;
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
            TextField::new('phonenumber'),
            AssociationField::new('user'),
        ];
    }
    
}
