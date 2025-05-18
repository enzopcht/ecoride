<?php

namespace App\Controller\dashboard;

use App\Entity\CreditTransaction;
use App\Entity\User;
use App\Form\EmployeType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(): Response
    {
        return $this->render('dashboard/admin/admin.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/admin/dashboard/create-employes', name: 'app_create_employes')]
    #[IsGranted('ROLE_ADMIN')]
    public function createEmployes(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_EMPLOYE']);

            $em->persist($user);

            $em->flush();

            $this->addFlash('primary', 'Le compte employé a bien été créé.');
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('dashboard/admin/create-employes.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}