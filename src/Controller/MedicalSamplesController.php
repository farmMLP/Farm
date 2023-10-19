<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\MedicalSamplesRepository;
use App\Entity\HealthCenter;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrdersRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\MedicalSamples;
use App\Repository\ProductsRepository;

use Knp\Component\Pager\PaginatorInterface;


class MedicalSamplesController extends AbstractController
{
    private $em;
    private $MedicalSamplesRepository;
    public function __construct(MedicalSamplesRepository $MedicalSamplesRepository, EntityManagerInterface $em)
    {
        $this->MedicalSamplesRepository = $MedicalSamplesRepository;
        $this->em = $em;
    }


    #[Route('/muestras', name: 'app_medical_samples')]
    public function index(MedicalSamplesRepository $MedicalSamplesRepository, Security $security, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $MedicalSamplesRepository->paginationQuery($security->getUser()->getHealthCenter()),
            $request->query->get('page', 1),
            10
        );
        return $this->render('medical_samples_user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/muestras/nuevo', name: 'app_medical_samples_new', methods: ['GET' , 'POST'])]
    public function create(Security $security, Request $request, ProductsRepository $productsRepository): Response
    {
        if ($request->isMethod('POST')){
          $quantitys = $request->request->all()['cantidad'];
          $products = $request->request->all()['producto'];
          $vencimientos = $request->request->all()['vencimiento'];
          if ((isset($request->request->all()['cantidad']) && (isset($request->request->all()['producto'])) && (isset($request->request->all()['vencimiento'])))) {
            foreach($products as $key => $value){
              $samples= new MedicalSamples();
              $samples->setStock($quantitys[$key]);
              $samples->setExpirationDate(new \Datetimeimmutable($vencimientos[$key]));
              $samples->setCreatedAt(new \DateTimeImmutable);
              $samples->setModifiedAt(new \DateTimeImmutable);
              $samples->setHealthCenter($security->getUser()->getHealthCenter());
              $product = $productsRepository->findOneById($products[$key]);
              $samples->setProduct($product);
              $this->MedicalSamplesRepository->save($samples , true);  
            }
            $this->redirectToRoute('app_medical_samples');
          }   
        }
        $products = $productsRepository->findAll();
        return $this->render('medical_samples_user/createNew.html.twig', [
            'productos'=>$products
        ]);
    }

    #[Route('/muestras/{id}', name: 'samples_show', methods: ['GET'])]
    public function show(Request $request, MedicalSamplesRepository $MedicalSamplesRepository, ProductsRepository $productsRepository, Security $sec, $id): Response
    {
       $samples=$MedicalSamplesRepository->findOneById($id);
       if($samples){
        if($samples->getHealthCenter() == $sec->getUser()->getHealthCenter()){
            return $this->render('medical_samples_user/showSamples.html.twig', [
                'sample'=>$samples     
            ]);
        } else {
          return $this->render('order/error.html.twig'); 
        }
      } else {
        return $this->render('order/error.html.twig'); 
      }
    }

  #[Route('muestra/{id}/update', name: 'updateStock', methods: ['POST'])]
  public function updateStock(Request $request, $id, Security $security){
    if($request->isMethod('POST')){
      $sample = $this->MedicalSamplesRepository->findOneById($id);
      if($security->getUser()){
        if($sample->getHealthCenter()->getId() == $security->getUser()->getHealthCenter()->getId()){
          $stock = $request->request->get('stockQuantity');
          $sample->setStock($stock);
          $this->MedicalSamplesRepository->save($sample,true);
        } else {
          return $this->render('order/error.html.twig');
        }
      }
      return $this->redirectToRoute('app_medical_samples');
    }
  }
  #[Route('muestra/{id}/delete', name: 'deleteSample', methods: ['POST'])]
  public function deleteSample(Request $request, $id, Security $security){
    if($request->isMethod('POST')){
      if($security->getUser()){
        $sample = $this->MedicalSamplesRepository->findOneById($id);
        if($sample->getHealthCenter()->getId() == $security->getUser()->getHealthCenter()->getId()){
          $this->em->remove($sample);
          $this->em->flush();
        } else {
          //test
          return $this->render('order/error.html.twig');
        }
      }
      return $this->redirectToRoute('app_medical_samples');
    }
  }
}