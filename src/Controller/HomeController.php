<?php /** @noinspection PhpPossiblePolymorphicInvocationInspection */

/** @noinspection PhpParamsInspection */

namespace App\Controller;

use App\Entity\Participant;
use App\Form\FiltreType;
use App\Repository\SortieRepository;
use DateTime;
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
        $participant=new Participant();
        $participant=$this->getUser();
        //dd($participant->getActif());
        if ($participant->getActif()==true) {

            //        dd($this->getUser());
            $filtres = $this->createForm(FiltreType::class);
            $filtres->handleRequest($request);
            $list = $sortieRepository->findAll();

            if ($filtres->isSubmitted() && $filtres->isValid()) {
                $datas = $filtres->getData();
                dump($datas);
                //            dd($datas);
                $list = $sortieRepository->search($datas, $this->getUser());

                if (in_array('passees', $datas['choices'])) {                                            //Si la case "sorties passées" a été cochée
                    foreach ($list as $key => $sortie) {                                                      //On parcourt les sorties issues de la requête
                        if ($sortie->getDateHeureDebut()->add($sortie->getDuree()) > new DateTime()) {        //si la date de debut plus la durée (soit la date de fin) de la sortie est posterieure à la date d'aujourd'hui
                            unset($list[$key]);                                                               //On l'enlève de la liste des sorties
                        }
                    }
                }
//            dd($list);
            }

            $message = '';                                      //pour pas qu'il me mette qu'il existe pas
            $bool = false;
            $nomUserConnecte = $this->getUser()->getNom();
            $dateCourante = new DateTime();                   //Recuperation date courante
//        dd($dateCourante);

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'liste' => $list,
                'ajourdhui' => $dateCourante,
                'message' => $message,
                'nomUser' => $nomUserConnecte,
                'bool' => $bool,
                'filtres' => $filtres->createView(),
            ]);
        }
        else
        {
            return $this->render('user/nope.html.twig');
        }
    }
/***********************  Permet de s'incrire a une sortie  **************************************/
    #[Route('/inscription/{idSortie}', name: 'inscription')]
    public function inscription(SortieRepository $sortieRepository, EntityManagerInterface $em, $idSortie,Request $request): Response
    {
        //Recuperation de la sortie
        $sortie = $sortieRepository->find($idSortie);
        $nbParticipant=$request->get('nbParticipant');

        //Si passe par url check le nombre de participant
        if ($nbParticipant<$sortie->getNbInscriptionMax())
        {

            //Ajout du participant a la sortie
            $sortie->addParticipant($this->getUser());
            $em->persist($sortie);
            $em->flush();
        }

        return $this->redirectToRoute('home');

    }
/*********************   Permet de se Desister d'une sortie  ******************************/
    #[Route('/seDesister', name: 'seDesister')]
    public function seDesister(SortieRepository $sortieRepository,Request $request, EntityManagerInterface $em): Response
    {

        //Recuperation de la sortie
        $idSortie=$request->get('idSortie');
        $sortie = $sortieRepository->find($idSortie);

        //On enleve le participant de la sortie
        $sortie->removeParticipant($this->getUser());
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('home');

    }


}
