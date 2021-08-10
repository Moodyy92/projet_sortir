<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestInscripController extends AbstractController
{
    #[Route('/test/inscrip', name: 'test_inscrip')]
    public function inscription(): Response
    {
        //recupere l'id de sortie


        //recupere l'id Participant


        //Insert Table sortie_participant


        return $this->render('test_inscrip/index.html.twig', [
            'controller_name' => 'TestInscripController',
        ]);
    }
}
