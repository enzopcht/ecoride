<?php

namespace App\Controller;

use App\Entity\Ride;
use App\Entity\Vehicle;
use App\Repository\RideRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vehicle', name: 'vehicle_')]
class VehicleController extends AbstractController
{
    #[Route('/vehicle/{id}/delete', name: 'delete')]
    #[IsGranted('ROLE_DRIVER')]
    public function delete(
        Vehicle $vehicle,
        EntityManagerInterface $em,
        RideRepository $rideRepository,
        VehicleRepository $vehicleRepository,
    ): RedirectResponse {
        $user = $this->getUser();

        if ($vehicle->getOwner() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $allRidesUser = $rideRepository->findBy([
            'driver' => $user,
        ]);
        $allUserVehicles = $vehicleRepository->findBy([
            'owner' => $user,
        ]);

        $activeVehicles = array_filter($allUserVehicles, fn($v) => !$v->isArchived());
        
        if (count($activeVehicles) < 2) {
            $this->addFlash('danger', 'Impossible de supprimer ce véhicule : vous devez conserver au moins un véhicule actif.');
            return $this->redirectToRoute('app_your_vehicles');
        }

        foreach ($allRidesUser as $ride) {
            if (
                $ride->getVehicle() === $vehicle &&
                in_array($ride->getStatus(), ['pending', 'active'])
            ) {
                $this->addFlash('danger', 'Impossible de supprimer ce véhicule : il est utilisé pour le trajet partant de ' . $ride->getDepartureCity() . ' -> ' . $ride->getArrivalCity() . ' le ' . $ride->getDepartureTime()->format('d/m/Y'));
                return $this->redirectToRoute('app_your_vehicles');
            }
        }
        
        $vehicle->setIsArchived(true);

        $em->flush();

        $this->addFlash('success', 'Votre voiture a été supprimé avec succès.');


        return $this->redirectToRoute('app_your_vehicles');
    }
}