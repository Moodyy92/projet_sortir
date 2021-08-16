<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieCreate;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/create', name: 'sortie_create')]
    public function new(Request $request, EntityManagerInterface $em,
                        EtatRepository $etatRepo, LieuRepository $lieuRepo): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieCreate::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lieuChoisi = $lieuRepo->findOneBy(['nom' => $_POST['lieu']]);
            $clickedButton = $form->getClickedButton()->getName();

            if ($clickedButton === "Publier"){
                $etatChoisi = $etatRepo->findOneBy(['libelle' => 'Ouverte']);
            }
            if ($clickedButton === "Creer"){
                $etatChoisi = $etatRepo->findOneBy(['libelle' => 'Créée']);
            }

            $sortie->setCampus($this->getUser()->getCampus());

            $sortie->setEtat($etatChoisi);                             //ETAT DE LA SORTIE = OBJET ETAT
            $sortie->setLieu($lieuChoisi);                             //LIEU DE LA SORTIE = OBJET LIEU
            $sortie->setCreatedAt(new \DateTimeImmutable());           //SORTIE CREE = DATE COURANTE
            $sortie->setOrganisateur($this->getUser());                //ORGANISATEUR = USER CONNECTE

            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('home');        //REDIRECTION APRES LA CREATION
        }

        $lieux = $lieuRepo->findAll();
        return $this->render('sortie/create.html.twig', [
            'lieux' => $lieux,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update/{idSortie}', name: 'sortie_update')]
    public function update(Request $request, EntityManagerInterface $em, $idSortie,
                           SortieRepository $repoSortie, LieuRepository $lieuRepo, EtatRepository $etatRepo): Response
    {
        $sortie = $repoSortie->find($idSortie);
        $form = $this->createForm(SortieCreate::class, $sortie);
        $form->handleRequest($request);

        //REDIRIGE SI L'USER CO != L'ORGANISATEUR DE LA SORTIE
        if($this->getUser() != $sortie->getOrganisateur()){ return $this->redirectToRoute('home'); }

        if ($form->isSubmitted() && $form->isValid()) {
            $lieuChoisi = $lieuRepo->findOneBy(['nom' => $_POST['lieu']]);
            $etatChoisi = $etatRepo->findOneBy(['libelle' => $_POST['etat']]);

            $sortie->setEtat($etatChoisi);                         //ETAT DE LA SORTIE = OBJET ETAT
            $sortie->setLieu($lieuChoisi);                         //LIEU DE LA SORTIE = OBJET LIEU
            $sortie->setLastUpdate(new \DateTimeImmutable());      //SORTIE UPDATE = DATE COURANTE
            $em->flush();
            return $this->redirectToRoute('home');        //REDIRECTION APRES L'UPDATE
        }

        $lieux = $lieuRepo->findAll();
        return $this->render('sortie/update.html.twig', [
            'sortie' => $sortie,
            'lieux' => $lieux,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/annuler/{idSortie}', name: 'sortie_annuler')]
    public function unset(EntityManagerInterface $em,
                          SortieRepository $sortieRepo, EtatRepository $etatRepo, $idSortie): Response
    {
        $etatChoisi = $etatRepo->findOneBy(['libelle' => 'Annulée']);
        $sortieChoisie = $sortieRepo->find($idSortie);

        if($sortieChoisie == null){
            throw $this->createNotFoundException("La sortie n'existe pas");
        }

        if($sortieChoisie->getDateHeureDebut() > new \DateTime() && $this->getUser() === $sortieChoisie->getOrganisateur()){
            $sortieChoisie->setEtat($etatChoisi);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }
}
