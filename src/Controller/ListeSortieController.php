<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListeSortieController extends AbstractController
{
    #[Route('/list', name: 'liste_sortie')]
    public function index(): Response
    {

        return $this->render('home/index.html.twig', [
            'controller_name' => 'ListeSortieController',

        ]);
    }
}
