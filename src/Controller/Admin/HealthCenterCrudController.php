<?php

namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use App\Entity\HealthCenter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Repository\HealthCenterRepository;
use App\Repository\MedicalSamplesRepository;

class HealthCenterCrudController extends AbstractCrudController
{
    public function __construct(EntityManagerInterface $em, HealthCenterRepository $healthCenterRepository, MedicalSamplesRepository $medicalSamples){
      $this->em = $em;
      $this->healthCenterRepository = $healthCenterRepository;
      $this->medicalSamplesRepository = $medicalSamples;
    }

    public static function getEntityFqcn(): string
    {
        return HealthCenter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
      return $crud->showEntityActionsInlined()->setPageTitle('index','Centros de Salud')
      ->setPageTitle('edit','Editar Centro de Salud')
      ->setPageTitle('new', 'Crear nuevo Centro de Salud');
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
      })
      ->update(Crud::PAGE_NEW,Action::SAVE_AND_ADD_ANOTHER,function(Action $action){
        return $action->setIcon('fa fa-edit')->setLabel('Guardar y seguir editando')->addCssClass('btn btn-secondary');
      })
      ->update(Crud::PAGE_NEW,Action::SAVE_AND_RETURN,function(Action $action){
        return $action->setIcon('fa fa-edit')->setLabel('Crear nuevo Centro de Salud')->addCssClass('btn btn-primary');
      });
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id'),
            TextField::new('name', 'Centro de salud'),
            TextField::new('address', 'Dirección'),
            TextField::new('phonenumber', 'Teléfono'),
            AssociationField::new('user', 'Encargado'),
            AssociationField::new('shipmentDay', 'Dia de entrega')
        ];
    }
    
    public function detail(AdminContext $context): Response {
      $healthCenterId = $context->getRequest()->query->get('entityId');
      $healthCenter = $this->healthCenterRepository->findOneById($healthCenterId);
      $medicalSamples = $this->medicalSamplesRepository->findByHealthCenter($healthCenterId);
      return $this->render('admin/healthCenter.html.twig',
      [
        'healthCenter' => $healthCenter,
        'medicalSamples' => $medicalSamples
      ]);
    }
}
