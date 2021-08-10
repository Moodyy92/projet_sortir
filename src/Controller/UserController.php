<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route('/user/{mail}', name: 'user_monProfil')]
     */
    public function monProfil(string $mail): Response
    {

        return $this->render('user/monProfil.html.twig', [

        ]);
    }
}
