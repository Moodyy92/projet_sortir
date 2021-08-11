<?php

namespace App\Controller;

use App\Form\FiltreType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {
        $filtres = $this->createForm(FiltreType::class);

        $filtres->handleRequest($request);
        $list = $sortieRepository->findAll();
        if($filtres->isSubmitted() && $filtres->isValid()){
            $datas = $filtres->getData();
            $list = $sortieRepository->search($datas);
        }
        //pour pas qu'il me mette qu'il existe pas
        $message='';
        $bool=false;

        $nomUserConnecte = $this->getUser()->getNom();

        //Recuperation date courante
        $dateCourante=new \DateTime();
        //dd($dateCourante);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'liste' => $list,
            'ajourdhui' =>$dateCourante,
            'message' => $message,
            'nomUser'=>$nomUserConnecte,
            'bool'=>$bool,
            'filtres'=>$filtres->createView(),
        ]);
    }

    #[Route('/inscription', name: 'inscription')]
    public function inscription(SortieRepository $sortieRepository,Request $request, EntityManagerInterface $em): Response
    {

        $idSortie=$request->get('idSortie');
        $sortie = $sortieRepository->find($idSortie);
        $sortie->addParticipant($this->getUser());
        $em->persist($sortie);
        $em->flush();
        return $this->redirectToRoute('home');

//        Recuperation date courante Sinon il ne prend pas en compte mon IF de l'inscription
//        $dateCourante=new \DateTime();
//        dd($dateCourante);
//
//        $nomUserConnecte = $this->getUser()->getNom();
//        $bool=false;
//
//        $list = $sortieRepository->findAll();
//        dd($list);
//
//        Recuperation de l'id du Particpant
//        $idPartipant = $this->getUser()->getId();
//         dd($id);
//
//        Recuperation de l'id sortie...
//        dd($idSortie);
//
//        Insert l'inscription dans sortie_participant
//        $message=$sortieRepository->insertSortieParticipant($idPartipant,$idSortie);

//    Envoie du message succes ou deja inscrit

//        return $this->render('home/index.html.twig', [
//            'controller_name' => 'HomeController',
//            'liste' => $list,
//            'ajourdhui' =>$dateCourante,
//            'message' => $message,
//            'nomUser'=>$nomUserConnecte,
//            'bool'=>$bool
//
//        ]);
    }


}
