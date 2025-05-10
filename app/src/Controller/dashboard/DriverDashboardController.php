<?php

namespace App\Controller\dashboard;

use App\Document\DriverPreference;
use App\Repository\CreditTransactionRepository;
use App\Repository\ParticipationRepository;
use App\Repository\ReviewRepository;
use App\Repository\RideRepository;
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
                            RideRepository $rideRepository,
                            ): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */
        $preferences = $dm->getRepository(DriverPreference::class)->findOneBy(['userId' => $user->getId()]);

        $participationsPendingPassenger = $participationRepository->findParticipationsForPassengerByStatuses($user, ['pending'], ['confirmed', 'pending']);
        $participationsActivePassenger = $participationRepository->findParticipationsForPassengerByStatuses($user, ['active', 'completed'], ['confirmed']);
        $allParticipationsPassenger = array_merge($participationsPendingPassenger, $participationsActivePassenger);
        $ratingsByDriver = [];
        foreach ($allParticipationsPassenger as $participation) {
            $driver = $participation->getRide()->getDriver();
            $driverId = $driver->getId();
            if (!isset($ratingsByDriver[$driverId])) {
                $ratingsByDriver[$driverId] = $reviewRepository->getAverageRatingForUser($driver);
            }
        }
        
        $ridesActive = $rideRepository->findBy([
            'driver' => $user,
            'status' => 'active',
        ]);
        $balance = $transactionRepository->calculateUserBalance($user);
        $userRating = $reviewRepository->getAverageRatingForUser($user);

        return $this->render('dashboard/driver.html.twig', [
            'user' => $user,
            'preferences' => $preferences,
            'balance' => $balance,
            'participations_active_passenger' => $participationsActivePassenger,
            'ratings_by_driver' => $ratingsByDriver,
            'rides_active' => $ridesActive,
            'user_rating' => $userRating,
        ]);
    }
}