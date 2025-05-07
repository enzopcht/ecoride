<?php

namespace App\Controller;

use App\Repository\CreditTransactionRepository;
use App\Repository\ParticipationRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    // =========================
    // DASHBOARD PAR RÔLE
    // =========================
    #[Route('/dashboard/passager', name: 'app_dashboard_passager', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PASSENGER')]
    public function passager(
        ParticipationRepository $participationRepository,
        CreditTransactionRepository $transactionRepository,
        ReviewRepository $reviewRepository,
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $participationsPending = $participationRepository->findParticipationsForPassengerByStatuses($user, ['pending'], ['confirmed', 'pending']);
        $participationsActive = $participationRepository->findParticipationsForPassengerByStatuses($user, ['active', 'completed'], ['confirmed']);

        $allParticipations = array_merge($participationsPending, $participationsActive);
        $ratingsByDriver = [];

        foreach ($allParticipations as $participation) {
            $driver = $participation->getRide()->getDriver();
            $driverId = $driver->getId();

            if (!isset($ratingsByDriver[$driverId])) {
                $ratingsByDriver[$driverId] = $reviewRepository->getAverageRatingForUser($driver);
            }
        }
        $balance = $transactionRepository->calculateUserBalance($this->getUser());

        return $this->render('dashboard/passager.html.twig', [
            'user' => $this->getUser(),
            'participations_pending' => $participationsPending,
            'participations_active' => $participationsActive,
            'balance' => $balance,
            'ratings_by_driver' => $ratingsByDriver,
        ]);
    }

    #[Route('/dashboard/driver', name: 'app_dashboard_driver')]
    #[IsGranted('ROLE_DRIVER')]
    public function driver(): Response
    {
        return $this->render('dashboard/driver.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/employe', name: 'app_dashboard_employe')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function employe(): Response
    {
        return $this->render('dashboard/employe.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/admin', name: 'app_dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(): Response
    {
        return $this->render('dashboard/admin.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
