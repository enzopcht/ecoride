<?php

namespace App\Controller;

use App\Repository\ParticipationRepository;
use App\Repository\ReviewRepository;
use App\Repository\RideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyBookingsController extends AbstractController
{
    #[Route('/dashboard/my-bookings', name: 'app_driver_dashboard_bookings')]
    public function myBookings(
        ParticipationRepository $participationRepository,
        RideRepository $rideRepository,
        ReviewRepository $reviewRepository,
        ): Response
    {
        $user = $this->getUser();

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }
        $reviews = $reviewRepository->findBy([
            'author' => $user,
        ]);

        $participations = $participationRepository->findBy(['user' => $user]);
        foreach ($participations as $participation) {
            $driver = $participation->getRide()->getDriver();
            $driverId = $driver->getId();
            if (!isset($ratingsByDriver[$driverId])) {
                $ratingsByDriver[$driverId] = $reviewRepository->getAverageRatingForUser($driver);
            }
        }

        return $this->render('my_bookings/index.html.twig', [
            'participations' => $participations,
            'user' => $user,
            'ratings_by_driver' => $ratingsByDriver,
            'reviews' => $reviews,
        ]);
    }
}
