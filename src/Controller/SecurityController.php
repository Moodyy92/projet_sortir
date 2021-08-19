<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\SortieRepository;
use App\Repository\EtatRepository;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, SortieRepository $repoSortie, EtatRepository $repoEtat,
    EntityManagerInterface $em): Response
    {
        /*** ON RECUPERE LES SORTIES AU CHARGEMENT DU SITE ET ON CHANGE LEUR ETAT SELON LEUR DATE ***/
        $sorties = $repoSortie->findAll();
        $etatCloturee = $repoEtat->findOneBy(['libelle' => 'Cloturée']);
        $etatEnCours = $repoEtat->findOneBy(['libelle' => 'Activité en cours']);

        foreach($sorties as $sortie){
            $etatActuel = $sortie->getEtat();
            if($sortie->getDateLimiteInscription() < new \DateTime()
                && $etatActuel != $etatCloturee && $etatActuel != $etatEnCours) {
                $sortie->setEtat($etatCloturee);
            }
            if($sortie->getDateHeureDebut() < new \DateTime() && $etatActuel != $etatEnCours){
                $sortie->setEtat($etatEnCours);
            }
            $em->flush();
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
