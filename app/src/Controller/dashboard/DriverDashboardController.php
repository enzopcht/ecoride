<?php

namespace App\Controller\dashboard;

use App\Repository\CreditTransactionRepository;
use App\Repository\ParticipationRepository;
use App\Repository\ReviewRepository;
use App\Repository\RideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DriverDashboardController extends AbstractController
{
    #[Route('/driver/dashboard', name: 'app_dashboard_driver')]
    #[IsGranted('ROLE_DRIVER')]
    public function driver(
        CreditTransactionRepository $transactionRepository,
        ParticipationRepository $participationRepository,
        ReviewRepository $reviewRepository,
        RideRepository $rideRepository,
    ): Response {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */

        $participationsActivePassenger = $participationRepository->findBy([
            'user' => $user,
            'status' => ['active', 'waiting_passenger_review']
        ]);
        $ratingsByDriver = [];
        foreach ($participationsActivePassenger as $participation) {
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

        $rides = $rideRepository->findBy(
            ['driver' => $user],
            ['departure_time' => 'ASC']
        );
        $participations = [];
        foreach ($rides as $ride) {
            $participations = array_merge($participations, $participationRepository->findBy([
                'ride' => $ride,
            ]));
        }

        return $this->render('dashboard/driver/index.html.twig', [
            'user' => $user,
            'balance' => $balance,
            'participations_active_passenger' => $participationsActivePassenger,
            'ratings_by_driver' => $ratingsByDriver,
            'rides_active' => $ridesActive,
            'user_rating' => $userRating,
            'rides' => $rides,
            'participations' => $participations,
        ]);
    }
    #[Route('/driver/dashboard/your-rides', name: 'app_driver_your_rides')]
    public function yourRides(
        RideRepository $rideRepository,
        ReviewRepository $reviewRepository,
    ): Response {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */
        $activeRides = $rideRepository->findBy(
            [
                'driver' => $user,
                'status' => ['active']
            ],
            ['departure_time' => 'ASC']
        );
        $nextRides = $rideRepository->findBy(
            [
                'driver' => $user,
                'status' => ['pending']
            ],
            ['departure_time' => 'ASC']
        );
        $previousRides = $rideRepository->findBy(
            [
                'driver' => $user,
                'status' => ['completed', 'canceled']
            ],
            ['departure_time' => 'DESC']
        );
        $userRating = $reviewRepository->getAverageRatingForUser($user);
        return $this->render('dashboard/driver/your-rides.html.twig', [
            'user' => $user,
            'active_rides' => $activeRides,
            'next_rides' => $nextRides,
            'previous_rides' => $previousRides,
            'user_rating' => $userRating,
        ]);
    }
    #[Route('/driver/dashboard/manage-your-bookings', name: 'app_driver_manage_your_bookings')]
    public function manageYourBookings(
        RideRepository $rideRepository,
        ParticipationRepository $participationRepository,
    ): Response {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */
        $rides = $rideRepository->findBy(
            [
                'driver' => $user,
            ],
            ['departure_time' => 'DESC']
        );
        $participations = [];
        foreach ($rides as $ride) {
            $participations = array_merge($participations, $participationRepository->findBy([
                'ride' => $ride,
            ]));
        }
        return $this->render('dashboard/driver/manage-your-bookings.html.twig', [
            'user' => $user,
            'rides' => $rides,
            'participations' => $participations,
        ]);
    }
}
