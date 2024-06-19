<?php

namespace App\Controller;

use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function listAll(SerieRepository $repository): Response
    {
        $allSeries=$repository->findAll();
        return $this->render('main/home.html.twig', [
            'allSeries' => $allSeries,
        ]);
    }

    #[Route('/details/{id]' ,name:'details')]
    public function details(SerieRepository $repository,$id): Response
    {
        return $this->render('main/details.html.twig',[
            'controller_name'=> 'MainController',
        ]);
    }
}
