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
use App\Repository\SortieRepository;

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

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])] //MODIFIER LE PROFIL
    public function edit(Request $request, Participant $participant): Response
    {
        //Seul l'utilisateur connecté peut modifier son propre profil
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

            // PHOTO DE PROFIL
            $photo = $form->get('photo')->getData();
            $fichier = md5(uniqid()).'.'.$photo->guessExtension();
            $img = new Photo();
            $img->setNom($fichier);
            $participant->setPhoto($img);
            $photo->move(
                $this->getParameter('photos'),
                $fichier
            );



            $this->addFlash('success', 'Vous avez bien mis à jour vos informations de profil');

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'user_delete', methods: ['GET'])]
    public function delete( Participant $participant, EntityManagerInterface $entityManager, SortieRepository $sortieRepository): Response
    {
            $sorties =$sortieRepository->findBy(['organisateur'=>$participant]);

           if ($this->getUser()->getRoles()[0] == 'ROLE_ADMIN'){
               foreach($sorties as $sortie){
                   $entityManager->remove($sortie);
               }
               $entityManager->remove($participant);
                $entityManager->flush();
    }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
