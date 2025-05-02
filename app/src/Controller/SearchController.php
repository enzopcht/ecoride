<?php

namespace App\Controller;

use App\Form\SearchRideType;
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
    public function results(Request $request): Response
    {
        $form = $this->createForm(SearchRideType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // 🔧 TRAJETS SIMULÉS
            $rides = [
                [
                    'departure' => 'Paris',
                    'arrival' => 'Lyon',
                    'date1' => new \DateTime('+1 day 14:00'),
                    'date2' => new \DateTime('+1 day 18:00'),
                    'driver' => 'Alice',
                    'vehicle' => 'Peugeot 208',
                    'energy' => 'thermique',
                    'eco' => false,
                    'price' => 8,
                    'seats' => 2
                ],
                [
                    'departure' => 'Paris',
                    'arrival' => 'Lyon',
                    'date1' => new \DateTime('+1 day 18:00'),
                    'date2' => new \DateTime('+1 day 21:00'),
                    'driver' => 'Julien',
                    'vehicle' => 'Tesla Model 3',
                    'energy' => 'électrique',
                    'eco' => true,
                    'price' => 10,
                    'seats' => 1
                ]
            ];

            return $this->render('search/results.html.twig', [
                'searchForm' => $form->createView(),
                'data' => $data,
                'rides' => $rides,
            ]);
        }

        return $this->redirectToRoute('app_search');
    }
}
