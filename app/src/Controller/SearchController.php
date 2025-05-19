<?php

namespace App\Controller;

use App\Form\SearchRideType;
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
        RideRepository $rideRepository
        ): Response
    {
        $form = $this->createForm(SearchRideType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data['date'] < (new \DateTime())->setTime(0, 0)) {
                $this->addFlash('warning', 'Impossible de rechercher un trajet dans le passé.');
                return $this->redirectToRoute('app_search');
            }
        
            $rides = $rideRepository->findRidesBySearchData(
                $data['departure'],
                $data['arrival'],
                $data['date']
            );

            $alternateRide = $rideRepository->findNextRideAfterDate(
                $data['departure'],
                $data['arrival'],
                $data['date']
            );

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
                'alternateForm' => $alternateForm->createView(),
            ]);
        }

        return $this->redirectToRoute('app_search');
    }
}
