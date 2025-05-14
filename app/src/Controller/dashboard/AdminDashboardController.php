<?php

namespace App\Controller\dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(): Response
    {
        return $this->render('dashboard/admin.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}