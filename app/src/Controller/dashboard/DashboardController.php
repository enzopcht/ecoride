<?php

namespace App\Controller\dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    private function hasRole(string $role): bool
    {
        return in_array($role, $this->getUser()?->getRoles() ?? [], true);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->hasRole('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_dashboard_admin');
        }

        if ($this->hasRole('ROLE_EMPLOYE')) {
            return $this->redirectToRoute('app_dashboard_employe');
        }

        if ($this->hasRole('ROLE_DRIVER')) {
            return $this->redirectToRoute('app_dashboard_driver');
        }

        return $this->redirectToRoute('app_dashboard_passager');
    }
}
