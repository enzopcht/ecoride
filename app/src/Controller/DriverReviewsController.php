<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DriverReviewsController extends AbstractController
{
    #[Route('/reviews/{pseudo}', name: 'app_driver_reviews')]
    public function index(
        UserRepository $userRepository,
        ReviewRepository $reviewRepository,
        string $pseudo,
    ): Response {
        $driver = $userRepository->findOneBy(['pseudo' => $pseudo]);

        if (!$driver) {
            throw $this->createNotFoundException('Conducteur non trouvé.');
        }
        $driverReviews = $reviewRepository->findBy(
            ['target' => $driver,
            'validated' => true],
            ['created_at' => 'ASC']
        );
        return $this->render('driver_reviews/index.html.twig', [
            'driver' => $driver,
            'driverReviews' => $driverReviews,
        ]);
    }
}
