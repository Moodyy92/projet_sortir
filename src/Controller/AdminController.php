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
    public function index(Request $request ,EntityManagerInterface $entityManager,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $newParticipant = new Participant();
        $form = $this->createForm(ParticipantType::class, $newParticipant);
        $form->handleRequest($request);

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

            //Et Boom en Bdd...
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newParticipant);
            $entityManager->flush();

            $this->addFlash('success','Participant Ajouter!');

            return $this->redirectToRoute('admin');

        }

        return $this->render('admin/index.html.twig',
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
