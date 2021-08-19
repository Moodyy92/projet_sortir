<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
/******************************   Creation d'un nouveau participant (Admin)   *******************************************************/
    #[Route('/addParticipant', name: 'addParticipant')]
    public function new(Request $request ,EntityManagerInterface $entityManager,UserPasswordEncoderInterface $passwordEncoder): Response
    {

        //Declaration de l'entity participant
        $newParticipant = new Participant();

        //Creation du formulaire
        $form = $this->createForm(ParticipantType::class, $newParticipant);
        $form->handleRequest($request);

        //Si 'isSubmitted' = |les validations du formulaire| et 'isValid' = |les validations des Asserts Participant|
        if ($form->isSubmitted() && $form->isValid())
        {
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
            //Envoie en Bdd...
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newParticipant);
            $entityManager->flush();

            $this->addFlash('success','Participant Ajouter!');

            return $this->redirectToRoute('addParticipant');

        }


        return $this->render('admin/formParticipant.html.twig',
            [

            'form' => $form->createView()
             ]);
    }

    #[Route('/admin/actDes', name: 'actDes')]
    public function activerDesactiverPart(Request $request,EntityManagerInterface $entityManager )
    {
        $idParticipant=$request->get('idParticipant');
        $participant = new Participant();
        $participant= $entityManager->getRepository('App:Participant')->find((int)$idParticipant);

        if ($participant->getActif()==true)
        {
            $participant->setActif(false);
        }
        else
        {
            $participant->setActif(true);
        }
        $entityManager->flush($participant);
        return $this->redirectToRoute('user_index');

    }
}
