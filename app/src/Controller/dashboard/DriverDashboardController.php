<?php

namespace App\Controller\dashboard;

use App\Document\DriverPreference;
use App\Repository\CreditTransactionRepository;
use App\Repository\ParticipationRepository;
use App\Repository\ReviewRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DriverDashboardController extends AbstractController
{
    #[Route('/driver/dashboard', name: 'app_dashboard_driver')]
    #[IsGranted('ROLE_DRIVER')]
    public function driver(
                            DocumentManager $dm,
                            CreditTransactionRepository $transactionRepository,
                            ParticipationRepository $participationRepository,
                            ReviewRepository $reviewRepository,
                            ): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */
        $preferences = $dm->getRepository(DriverPreference::class)->findOneBy(['userId' => $user->getId()]);
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

        return $this->render('dashboard/driver.html.twig', [
            'user' => $user,
            'preferences' => $preferences,
            'balance' => $balance,
            'participations_pending' => $participationsPending,
            'participations_active' => $participationsActive,
            'ratings_by_driver' => $ratingsByDriver,
        ]);
    }
}