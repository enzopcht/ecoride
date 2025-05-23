<?php 

namespace App\Controller\dashboard;

use App\Repository\CreditTransactionRepository;
use App\Repository\ParticipationRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserDashboardController extends AbstractController
{
    #[Route('/passenger/dashboard', name: 'app_dashboard_passager', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PASSENGER')]
    public function passager(
        ParticipationRepository $participationRepository,
        CreditTransactionRepository $transactionRepository,
        ReviewRepository $reviewRepository,
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $participationsActive = $participationRepository->findBy([
            'user' => $user,
            'status' => ['active', 'waiting_passenger_review']
        ]);

        $ratingsByDriver = [];

        foreach ($participationsActive as $participation) {
            $driver = $participation->getRide()->getDriver();
            $driverId = $driver->getId();

            if (!isset($ratingsByDriver[$driverId])) {
                $ratingsByDriver[$driverId] = $reviewRepository->getAverageRatingForUser($driver);
            }
        }
        $balance = $transactionRepository->calculateUserBalance($this->getUser());

        return $this->render('dashboard/passager.html.twig', [
            'user' => $this->getUser(),
            'participations_active' => $participationsActive,
            'balance' => $balance,
            'ratings_by_driver' => $ratingsByDriver,
        ]);
    }
}