<?php

namespace App\Controller;

use App\Form\SearchRideType;
use App\Repository\ReviewRepository;
use App\Repository\RideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchRideType::class);

        return $this->render('search/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route('/search/results', name: 'app_search_results', methods: ['GET', 'POST'])]
    public function results(
        Request $request,
        RideRepository $rideRepository,
        ReviewRepository $reviewRepository,
    ): Response {
        $form = $this->createForm(SearchRideType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['date'] < (new \DateTime())->setTime(0, 0)) {
                $this->addFlash('warning', 'Impossible de rechercher un trajet dans le passé.');
                return $this->redirectToRoute('app_search');
            }
            $ratingsByDriver = [];

            $rides = $rideRepository->findRidesBySearchData(
                $data['departure'],
                $data['arrival'],
                $data['date']
            );

            foreach ($rides as $ride) {
                $driver = $ride->getDriver();
                $driverId = $driver->getId();
                if (!isset($ratingsByDriver[$driverId])) {
                    $ratingsByDriver[$driverId] = $reviewRepository->getAverageRatingForUser($driver);
                }
            }

            $alternateRide = $rideRepository->findNextRideAfterDate(
                $data['departure'],
                $data['arrival'],
                $data['date']
            );
            // foreach ($alternateRide as $ride) {
            //     $driver = $ride->getDriver();
            //     $driverId = $driver->getId();
            //     if (!isset($ratingsByDriver[$driverId])) {
            //         $ratingsByDriver[$driverId] = $reviewRepository->getAverageRatingForUser($driver);
            //     }
            // }
            $alternateForm = $this->createForm(SearchRideType::class, [
                'departure' => $data['departure'],
                'arrival' => $data['arrival'],
                'date' => $alternateRide?->getDepartureTime(),
            ]);

            return $this->render('search/results.html.twig', [
                'searchForm' => $form->createView(),
                'data' => $data,
                'rides' => $rides,
                'alternateRide' => $alternateRide,
                'ratings_by_driver' => $ratingsByDriver,
                'alternateForm' => $alternateForm->createView(),
            ]);
        }

        return $this->redirectToRoute('app_search');
    }
}
