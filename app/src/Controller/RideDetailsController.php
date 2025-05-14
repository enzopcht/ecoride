<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RideDetailsController extends AbstractController
{
    #[Route('/ride/details', name: 'app_ride_details')]
    public function index(): Response
    {
        return $this->render('ride_details/index.html.twig', [
        ]);
    }
}
