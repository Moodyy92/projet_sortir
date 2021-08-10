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
            $etatChoisi = $etatRepo->findOneBy(['libelle' => 'Ouverte']);

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

    #[Route('/annuler/{idSortie}', name: 'sortie_annuler')]
    public function unset(EntityManagerInterface $em,
                          SortieRepository $sortieRepo, EtatRepository $etatRepo, $idSortie): Response
    {
        $etatChoisi = $etatRepo->findOneBy(['libelle' => 'Annulée']);
        $sortieChoisie = $sortieRepo->findOneBy(['id' => $idSortie]);

        if($sortieChoisie->getDateHeureDebut() < new \DateTime()){
            $sortieChoisie->setEtat($etatChoisi);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }
}
