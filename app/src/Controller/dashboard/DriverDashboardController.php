<?php

namespace App\Controller\dashboard;

use App\Document\DriverPreference;
use App\Repository\CreditTransactionRepository;
use App\Repository\ParticipationRepository;
use App\Repository\ReviewRepository;
use App\Repository\RideRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('dashboard/driver/index.html.twig', [
            'user' => $user,

            'balance' => $balance,
            'participations_active_passenger' => $participationsActivePassenger,
            'ratings_by_driver' => $ratingsByDriver,
            'rides_active' => $ridesActive,
            'user_rating' => $userRating,
        ]);
    }
    #[Route('/driver/dashboard/preferences', name: 'app_driver_preferences')]
    public function preferences(
        DocumentManager $dm,
    ): Response {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */
        $preferences = $dm->getRepository(DriverPreference::class)->findOneBy(['userId' => $user->getId()]);
        return $this->render('dashboard/driver/preferences.html.twig', [
            'preferences' => $preferences,
        ]);
    }
    #[Route('/driver/dashboard/preferences/update', name: 'app_driver_preferences_update', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function updatePreferences(
        Request $request,
        DocumentManager $dm
    ): Response {
        $user = $this->getUser();

        if (!$this->isCsrfTokenValid('update_preferences', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide');
        }

        /** @var \App\Entity\User $user */
        $preference = $dm->getRepository(\App\Document\DriverPreference::class)->findOneBy(['userId' => $user->getId()]);
        if (!$preference) {
            $preference = new \App\Document\DriverPreference();
            $preference->setUserId($user->getId());
        }

        $preference->setMusicAllowed($request->request->has('musicAllowed'));
        $preference->setSmokingAllowed($request->request->has('smokingAllowed'));
        $preference->setAnimalsAllowed($request->request->has('animalsAllowed'));

        $dm->persist($preference);
        $dm->flush();

        $this->addFlash('success', 'Préférences mises à jour avec succès.');

        return $this->redirectToRoute('app_driver_preferences');
    }
}
