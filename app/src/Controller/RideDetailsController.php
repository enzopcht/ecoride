<?php

namespace App\Controller;

use App\Document\DriverPreference;
use App\Entity\Ride;
use App\Repository\ReviewRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RideDetailsController extends AbstractController
{
    #[Route('/ride/{id}/details', name: 'app_ride_details', methods:['GET'])]
    public function index(
        Ride $ride,
        DocumentManager $dm,
        ReviewRepository $reviewRepository,
    ): Response
    {
        $driver = $ride->getDriver();
        $user = $this->getUser();

        $preferences = $dm->getRepository(DriverPreference::class)->findOneBy([
            'userId' => $driver->getId(),
        ]);

        $driverRating = $reviewRepository->getAverageRatingForUser($driver);
        $numberOfReviewsDriver = $reviewRepository->findBy([
            'target' => $driver
        ]);

        return $this->render('ride_details/index.html.twig', [
            'ride' => $ride,
            'preferences' => $preferences,
            'driver_rating' => $driverRating,
            'number_of_reviews_driver' => $numberOfReviewsDriver,
            'user' => $user,
        ]);
    }
}
