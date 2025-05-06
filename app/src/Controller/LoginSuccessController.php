<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginSuccessController extends AbstractController
{
    #[Route('/login/success', name: 'login_success')]
    public function index(): RedirectResponse
    {
        $user = $this->getUser();
        /**
         * @var \App\Entity\User $user
         */
        $pseudo = $user ? $user->getPseudo() : 'Utilisateur';

        $this->addFlash('success', "Connexion réussie. Bienvenue sur EcoRide, $pseudo !");

        return $this->redirectToRoute('app_home');
    }
}
