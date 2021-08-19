<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\PhotoDeProfil;
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
    public function edit(Request $request, Participant $participant,UserPasswordEncoderInterface $passwordEncoder): Response
    {


        $participant=$this->getUser();

        $formUpdate = $this->createForm(ModifParticipantType::class, $participant);
        $formUpdate->handleRequest($request);

        $verifPassword=new Participant();



        if ($formUpdate->isSubmitted()&&$formUpdate->isValid()){


            $this->addFlash('success', 'Vous avez bien mis à jour vos informations de profil');

            $photo = $formUpdate->get('photo')->getData();
            if($photo != null){
                $fichier = md5(uniqid()).'.'.$photo->guessExtension();
                $img = new PhotoDeProfil();
                $img->setNom($fichier);
                $participant->setPhoto($img);
                $photo->move(
                    $this->getParameter('photos'),
                    $fichier
                );
            }
           else {
              // $participant->setPhoto($photo);
            }

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
