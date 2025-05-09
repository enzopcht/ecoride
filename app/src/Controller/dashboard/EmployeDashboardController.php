<?php

namespace App\Controller\dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EmployeDashboardController extends AbstractController
{
    #[Route('/employe/dashboard', name: 'app_dashboard_employe')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function employe(): Response
    {
        return $this->render('dashboard/employe.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}