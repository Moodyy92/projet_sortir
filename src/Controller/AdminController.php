<?php

namespace App\Controller;


use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Ville;
use App\Form\NewCampus;
use App\Form\NewLieu;
use App\Form\NewVille;
use App\Form\ParticipantType;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, LieuRepository $repoLieu, VilleRepository $repoVille, CampusRepository $repoCampus): Response
    {

        /*******************************************************************************************************************************
         *                                          Gestion du formulaire Participant par Admin                                        *
         ******************************************************************************************************************************/
        $newParticipant = new Participant();
        $form = $this->createForm(ParticipantType::class, $newParticipant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Role user par defaut
            $newParticipant->setRoles(array("ROLE_USER"));
            $newParticipant->setActif('true');

            //Date courante
            $newParticipant->setCreatedAt(new \DateTimeImmutable());

            //Encodage du mot de passe
            $newParticipant->setPassword(
                $passwordEncoder->encodePassword(
                    $newParticipant,
                    $form->get('password')->getData()
                )
            );

            //Envoi en bdd
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newParticipant);
            $entityManager->flush();

            $this->addFlash('success', 'Participant Ajouté!');
        }
        /*******************************************************************************************************************************
         *                                          Gestion du formulaire lieux par Admin                                              *
         ******************************************************************************************************************************/
        $lieu = new Lieu();
        $formLieu = $this->createForm(NewLieu::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $ville = $repoVille->findOneBy(['nom' => $request->request->get('ville')]);
            $lieu->setVille($ville);
            $entityManager->persist($lieu);
            $entityManager->flush();
        }

        /**************************************************************************************************************
         *                                      Gestion des villes Admin                                              *
         ***************************************************************************************************************/
        $ville = new Ville();

        $formVille = $this->createForm(NewVille::class, $ville);

        $formVille->handleRequest($request);

        if ($formVille->isSubmitted() && $formVille->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
        }
        /**************************************************************************************************************
         *                                      Gestion des campus Admin                                              *
         ***************************************************************************************************************/
        $campus = new Campus();
        $formCampus = $this->createForm(NewCampus::class, $campus);
        $formCampus->handleRequest($request);

        if ($formCampus->isSubmitted() && $formCampus->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();
            $this->redirectToRoute('gestion_campus');
        }

        $campusList = $repoCampus->findAll();
        $villeList = $repoVille->findAll();
        $lieuList = $repoLieu->findAll();


        return $this->render('admin/index.html.twig',
            [
                'formCampus' => $formCampus->createView(),
                'form' => $form->createView(),
                'formLieu' => $formLieu->createView(),
                'formVille' => $formVille->createView(),
                'lieux' => $lieuList,
                'villes' => $villeList,
                'campus' => $campusList,
            ]);
    }

    #[Route('/actDes', name: 'actDes')]
    public function activerDesactiverPart(Request $request, EntityManagerInterface $entityManager)
    {
        $idParticipant = $request->get('idParticipant');
        $participant = new Participant();
        $participant = $entityManager->getRepository('App:Participant')->find((int)$idParticipant);

        if ($participant->getActif() == true) {
            $participant->setActif(false);
        } else {
            $participant->setActif(true);
        }
        $entityManager->flush($participant);
        return $this->redirectToRoute('user_index');

    }



    //----------------------------------- SUPPRIMER DES LIEUX ------------------------------------------//
    #[Route('/delete/lieu/{idLieu}', name: 'delete_lieu')]
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
