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
        /*** CREER UN OBJET SORTIE, LE CONSTRUIT VIA LE FORM ET L'INSERT ***/

        $sortie = new Sortie();
        $form = $this->createForm(SortieCreate::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lieuChoisi = $lieuRepo->findOneBy(['nom' => $_POST['lieu']]);
            $clickedButton = $form->getClickedButton()->getName();

            /*** L'ETAT DE LA SORTIE DEPEND DU BOUTTON CLIQUE  ***/
            if ($clickedButton === "Publier"){
                $etatChoisi = $etatRepo->findOneBy(['libelle' => 'Ouverte']);
            }
            if ($clickedButton === "Creer"){
                $etatChoisi = $etatRepo->findOneBy(['libelle' => 'Créée']);
            }

            $sortie->setCampus($this->getUser()->getCampus());         //CAMPUS DE LA SORTIE = CAMPUS DE L'USER CO
            $sortie->setEtat($etatChoisi);                             //ETAT DE LA SORTIE = OBJET ETAT
            $sortie->setLieu($lieuChoisi);                             //LIEU DE LA SORTIE = OBJET LIEU
            $sortie->setCreatedAt(new \DateTimeImmutable());
            $sortie->setOrganisateur($this->getUser());                //ORGANISATEUR = USER CONNECTE
            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute('home');
        }

        $lieux = $lieuRepo->findAll(); //LISTE DES LIEUX SELECTIONNABLES EN TWIG
        return $this->render('sortie/create.html.twig', [
            'lieux' => $lieux,
            'form' => $form->createView(),
        ]);
    }

    /***************************************************************************************************************/

    #[Route('/update/{idSortie}', name: 'sortie_update')]
    public function update(Request $request, EntityManagerInterface $em, $idSortie,
                           SortieRepository $repoSortie, LieuRepository $lieuRepo, EtatRepository $etatRepo): Response
    {
        /*** MISE A JOUR DE L'INSTANCE DE SORTIE RECUPEREE VIA l'idSORTIE ET PASSEE EN PARAM DU FORM ***/

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
            $sortie->setLastUpdate(new \DateTimeImmutable());
            $em->flush();
            return $this->redirectToRoute('home');
        }

        $lieux = $lieuRepo->findAll();
        return $this->render('sortie/update.html.twig', [
            'sortie' => $sortie,
            'lieux' => $lieux,
            'form' => $form->createView(),
        ]);
    }

    /***************************************************************************************************************/

    #[Route('/annuler/{idSortie}', name: 'sortie_annuler')]
    public function unset(EntityManagerInterface $em,
                          SortieRepository $sortieRepo, EtatRepository $etatRepo, $idSortie): Response
    {
        /** La méthode récupère via l'idSortie, la sortie choisie et une instance de l'objet Etat en mode "Annulée".
        *** Si la date d'aujourd'hui est inférieure à la date de la sortie,
        *** Si l'userCo est l'organisateur | admin,
        *** On annule la sortie ***/

        $sortieChoisie = $sortieRepo->find($idSortie);
        $etatChoisi = $etatRepo->findOneBy(['libelle' => 'Annulée']);

        if(new \DateTime() < $sortieChoisie->getDateHeureDebut()){
            if($this->getUser() == $sortieChoisie->getOrganisateur() || $this->isGranted('ROLE_ADMIN')){
                $sortieChoisie->setEtat($etatChoisi);
                $em->flush();
            }
        }

        return $this->redirectToRoute('home');
    }
}
