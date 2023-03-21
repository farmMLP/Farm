<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\HealthCenterRepository;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
  
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function __construct(HealthCenterRepository $healthCenterRepo, User $user, UserPasswordHasherInterface $userPasswordHasher, UserRepository $users){
      $this->healthCenterRepo = $healthCenterRepo;
      $this->user = $user;
      $this->userPasswordHasher = $userPasswordHasher;
      $this->users = $users;
    }

    public function configureCrud(Crud $crud): Crud
    {
      return $crud->showEntityActionsInlined()
      ->setPageTitle('index','Usuarios')
      ->setPageTitle('edit','Editar Usuario');
    }

    public function configureActions(Actions $actions): Actions
    {
      return $actions
      ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
        return $action->setIcon('fa fa-plus')->addCssClass('btn btn-primary')->setLabel('Crear nuevo Usuario')->linkToCrudAction('new');
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
            EmailField::new('email', 'Email'),
            TextField::new('name', 'Nombre'),
            TextField::new('lastname', 'Apellido'),
            NumberField::new('dni', 'DNI'),
            TextField::new('password', 'Contraseña')->hideOnIndex(),
            AssociationField::new('healthCenter','Centro de salud')
        ];
    }

    public function new(AdminContext $context): Response
    { 
      $user = new User();
      // $this->user = new User();
      $healthCenters = $this->healthCenterRepo->findAll();
      $passworderror = false;
      if ($context->getRequest()->isMethod('POST')){
        $name = $context->getRequest()->request->get('name');
        $email = $context->getRequest()->request->get('email');
        $lastname = $context->getRequest()->request->get('lastname');
        $dni = $context->getRequest()->request->get('dni');
        $password = $context->getRequest()->request->get('password');
        $passwordrepeat = $context->getRequest()->request->get('passwordrepeat');
        $healthCenter = $context->getRequest()->request->get('healthcenter');
        $hcEntity = $this->healthCenterRepo->findOneById($healthCenter);
        if ($password === $passwordrepeat) {
          $user->setPassword(
            $this->userPasswordHasher->hashPassword(
              $user,
              $password
              )
            );
          $user->setName($name);
          $user->setLastname($lastname);
          // dd($context->getRequest()->request->all());
          // hay que setearle una entidad, no un id, por lo que hay que buscar el health center primero.
          // dd($this->healthCenterRepo->findOneById($healthCenter));
          $user->setHealthCenter($this->healthCenterRepo->findOneById($healthCenter));
          $user->setDni($dni);
          $user->setEmail($email);
          $user->setRoles(['ROLE_USER']);
          $this->users->save($user, true);
          $hcEntity->setUser($user);
          $this->healthCenterRepo->save($hcEntity, true);
          return $this->redirect('/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController');
        } else {
          $passworderror = true;
          return $this->render('admin/createUser.html.twig', [
            "healthCenters" => $healthCenters,
            "passworderror" => $passworderror
          ]);
        }
        
      }
      return $this->render('admin/createUser.html.twig', [
        "healthCenters" => $healthCenters,
        "passworderror" => $passworderror
      ]);
    }
}
