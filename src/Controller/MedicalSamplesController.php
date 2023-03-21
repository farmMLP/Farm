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
          // id de productos
          // dd($request->request->all()['producto']);
          // cantidades
          //  dd($request->request->all()['cantidad']);
          $quantitys = $request->request->all()['cantidad'];
          $products = $request->request->all()['producto'];
          if ((isset($request->request->all()['cantidad']) && (isset($request->request->all()['producto'])))) {
            
            foreach($products as $key => $value){
              $medicalSample = $this->MedicalSamplesRepository->findIfExistsByHealthCenter($security->getUser()->getHealthCenter(), $value);
              if($medicalSample){
                $medicalSample->addStock($quantitys[$key]);
                $this->MedicalSamplesRepository->save($medicalSample,true);
              } else {
                $samples= new MedicalSamples();
                $samples->setStock($quantitys[$key]);
                $samples->setExpirationDate(new \DateTimeImmutable);
                $samples->setHealthCenter($security->getUser()->getHealthCenter());
                $product = $productsRepository->findOneById($products[$key]);
                $samples->setProduct($product);
                $this->MedicalSamplesRepository->save($samples , true);  
              }
            }
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

        }
            else{
            return $this->render('order/error.html.twig'); 
            }

    }
    else{
        return $this->render('order/error.html.twig'); 
    }
}

}