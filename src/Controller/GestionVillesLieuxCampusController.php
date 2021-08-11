<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\NewCampus;
use App\Form\NewLieu;
use App\Form\NewVille;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VilleRepository;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;

class GestionVillesLieuxCampusController extends AbstractController
{

    //----------------------------------- TROUVER ET AJOUTER DES LIEUX ------------------------------------------//
    #[Route('/gestion/lieux', name: 'gestion_lieux')]
    public function lieux(VilleRepository $repoVille, LieuRepository $repoLieu,
                          Request $request, EntityManagerInterface $em): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(NewLieu::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ville = $repoVille->findOneBy(['nom' => $_POST['ville']]);
            $lieu->setVille($ville);
            $em->persist($lieu);
            $em->flush();
            $this->redirectToRoute('gestion_lieux');
        }

        $villeList = $repoVille->findAll();
        $lieuList = $repoLieu->findAll();
        return $this->render('gestion_villes_lieux_campus/lieux.html.twig', [
            'lieux' => $lieuList,
            'villes' => $villeList,
            'formLieu' => $form->createView()
        ]);
    }


    //----------------------------------- TROUVER ET AJOUTER DES CAMPUS ------------------------------------------//
    #[Route('/gestion/campus', name: 'gestion_campus')]
    public function campus(CampusRepository $repoCampus, Request $request, EntityManagerInterface $em): Response
    {
        $campus = new Campus();
        $form = $this->createForm(NewCampus::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($campus);
            $em->flush();
            $this->redirectToRoute('gestion_campus');
        }

        $campusList = $repoCampus->findAll();
        return $this->render('gestion_villes_lieux_campus/campus.html.twig', [
            'campus' => $campusList,
            'formCampus' => $form->createView()
        ]);
    }


    //----------------------------------- TROUVER ET AJOUTER DES VILLES ------------------------------------------//
    #[Route('/gestion/villes', name: 'gestion_villes')]
    public function villes(VilleRepository $repoVille, Request $request, EntityManagerInterface $em): Response
    {
        $ville = new Ville();
        $form = $this->createForm(NewVille::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ville);
            $em->flush();
            $this->redirectToRoute('gestion_villes');
        }

        $villeList = $repoVille->findAll();
        return $this->render('gestion_villes_lieux_campus/villes.html.twig', [
            'villes' => $villeList,
            'formVille' => $form->createView()
        ]);
    }


    //----------------------------------- SUPPRIMER DES LIEUX ------------------------------------------//
    #[Route('/gestion/delete/lieu/{idLieu}', name: 'delete_lieu')]
    public function deleteLieu(LieuRepository $repoLieu, SortieRepository $repoSortie,
                               $idLieu, EntityManagerInterface $em): Response
    {
        $lieu = $repoLieu->findOneBy(['id' => $idLieu]);
        $sorties = $repoSortie->findBy(['lieu' => $lieu]);
        foreach ($sorties as $sortie){ $em->remove($sortie); }  //SUPPRIME LES SORTIES ASSOCIEES A CE LIEU
        $em->remove($lieu);
        $em->flush();
        return $this->redirectToRoute('gestion_lieux');
    }


    //----------------------------------- SUPPRIMER DES CAMPUS ------------------------------------------//
    #[Route('/gestion/delete/campus/{idCampus}', name: 'delete_campus')]
    public function deleteCampus(CampusRepository $repoCampus, ParticipantRepository $repoParticipants,
                                 SortieRepository $repoSortie, $idCampus, EntityManagerInterface $em): Response
    {
        $campus = $repoCampus->findOneBy(['id' => $idCampus]);
        $participants = $repoParticipants->findBy(['campus' => $campus]);

        //SUPPRIME LES PARTICIPANTS ASSOCIES AU CAMPUS, & LES SORTIES DONT ILS SONT ORGANISATEURS
        foreach ($participants as $participant){
            $sorties = $repoSortie->findBy(['organisateur' => $participant]);
            foreach ($sorties as $sortie){ $em->remove($sortie); }
            $em->remove($participant);
        }
        $em->remove($campus);
        $em->flush();
        return $this->redirectToRoute('gestion_campus');
    }


    //----------------------------------- SUPPRIMER DES VILLES ------------------------------------------//
    #[Route('/gestion/delete/ville/{idVille}', name: 'delete_ville')]
    public function deleteVille(VilleRepository $repoVille, LieuRepository $repoLieu, SortieRepository $repoSortie,
                                $idVille, EntityManagerInterface $em): Response
    {
        $ville = $repoVille->findOneBy(['id' => $idVille]);
        $lieux = $repoLieu->findBy(['ville' => $ville]);

        //SUPPRIME LES LIEUX ASSOCIES A LA VILLE, & LES SORTIES ASSOCIEES AUX LIEUX
        foreach ($lieux as $lieu){
            $sorties = $repoSortie->findBy(['lieu' => $lieu]);
            foreach ($sorties as $sortie){ $em->remove($sortie); }
            $em->remove($lieu);
        }
        $em->remove($ville);
        $em->flush();
        return $this->redirectToRoute('gestion_villes');
    }
}
