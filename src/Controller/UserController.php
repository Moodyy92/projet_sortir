<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifParticipantType;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET', 'POST'])]
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }
/***********************            Creation Participant (Admin)             *****************************/
    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        //Creation du participant "vide"!
        $participant = new Participant();

        //Appelle du form ParticipantType avec le new participant en parametre
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        //Si Form est envoye et valide(Assert Participant) -> traitement du formulaire...
        if ($form->isSubmitted() && $form->isValid()) {

            //Creation en base de donnees...
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        //Envoie du formulaire en twig...
        return $this->renderForm('user/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(Participant $participant): Response
    {

        return $this->render('user/show.html.twig', [
            'participant' => $participant
         ]);

    }

    /***********************            Modifier Participant             *****************************/
    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participant $participant,UserPasswordEncoderInterface $passwordEncoder): Response
    {

        //Recuperation du Participant connecter
        $participant=$this->getUser();

        //Appelle du form ModifParticipantType avec le new participant en parametre
        $formUpdate = $this->createForm(ModifParticipantType::class, $participant);
        $formUpdate->handleRequest($request);

        //Si Form est envoye et valide(Assert Participant) -> traitement du formulaire...
        if ($formUpdate->isSubmitted()&&$formUpdate->isValid()){


            //Message de validation
            $this->addFlash('success', 'Vous avez bien mis à jour vos informations de profil');


            //Modification en base de donnees...
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'participant' => $participant,
            'formUpdate' => $formUpdate->createView(),
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, Participant $participant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
