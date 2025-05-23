<?php

namespace App\Controller;

use App\Form\SearchRideType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $form = $this->createForm(SearchRideType::class);

        return $this->render('home/index.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }
}
