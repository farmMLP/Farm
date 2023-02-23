<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HealthCenterRepository;

class HealthCenterController extends AbstractController
{

    #[Route('/centro/{healthcenter}', name: 'app_health_index')]
    public function index($healthcenter, HealthCenterRepository $healthcenters): Response
    {
        $MyHealthCenter = $healthcenters->findOneByName($healthcenter);
        return $this->render('healthCenter/index.html.twig', [
            'healthcenter' => $MyHealthCenter,
        ]);
    }

}
