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
        
        dd([
            'POST brut' => $request->request->all(),
            'Données filtrées' => $form->getData(),
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return $this->render('search/results.html.twig', [
                'searchForm' => $form->createView(),
                'data' => $data,
            ]);
        }

        return $this->redirectToRoute('app_search');
    }
}
