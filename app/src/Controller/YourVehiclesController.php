<?php

namespace App\Controller;

use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class YourVehiclesController extends AbstractController
{
    #[Route('/driver/your-vehicles', name: 'app_your_vehicles')]
    #[IsGranted('ROLE_DRIVER')]
    public function index(
        VehicleRepository $vehicleRepository,
    ): Response
    {
        $user = $this->getUser();

        $vehicles = $vehicleRepository->findBy([
            'owner' => $user,
        ]);

        return $this->render('your_vehicles/index.html.twig', [
            'user' => $user,
            'vehicles' => $vehicles,
        ]);
    }
}
