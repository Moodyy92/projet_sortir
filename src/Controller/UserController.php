<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

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

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participant $participant): Response
    {

        $form = $this->createForm(ParticipantType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($pseudo))
            {
                $pseudo->setPseudo($pseudo);
            }
            if (!empty($nom))
            {
                $nom->setNom($nom);
            }
            if (!empty($prenom))
            {
                $prenom->setPrenom($prenom);
            }
            if (!empty($telephone))
            {
                $telephone->setTelephone($telephone);
            }
            if (!empty($campus))
            {
                $campus->setCampus($campus);
            }
            if (!empty($email))
            {
                $email->setEmail($email);
            }
            if (!empty($password))
            {
                $password->setPassword($password);
            }

// mise en place de la mise à jour de la  photo (dans modifier profil)-------------------------------
//            $file = $form->get('picture')->getData();
//  //        $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
//            $fileName = $user->getFileName();
//            //Déplace le fichier dans le dossier upload
 //           try {
//                $file->move(
//                    $this->getParameter('upload_directory'),
//                    $fileName
//                );
//            } catch (FileException $e) { }
//
//            $user->setPicture($fileName);





            $this->addFlash('success', 'Vous avez bien mis à jour vos informations de profil');

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
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
