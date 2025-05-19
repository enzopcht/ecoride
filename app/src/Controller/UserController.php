<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/{id}/suspend', name: 'suspend', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function suspend(
        Request $request,
        User $user,
        EntityManagerInterface $em,
    ): RedirectResponse {
        $user->setSuspended(true);
        $em->flush();

        $this->addFlash('primary', 'Le compte de ' . $user . ' a été suspendu.');

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/{id}/reactivate', name: 'reactivate', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function reactivate(
        Request $request,
        User $user,
        EntityManagerInterface $em,
    ): RedirectResponse {
        $user->setSuspended(false);
        $em->flush();

        $this->addFlash('primary', 'Le compte de ' . $user . ' a été réactivé.');

        return $this->redirect($request->headers->get('referer'));
    }
}
