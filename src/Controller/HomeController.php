<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function index(SortieRepository $sortieRepository): Response
    {
        $list = $sortieRepository->findAll();
//    dd($list);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'liste' => $list,
        ]);
    }
}
