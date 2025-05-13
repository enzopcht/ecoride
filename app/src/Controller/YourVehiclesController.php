<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\Model\VehicleData;
use App\Form\VehicleType;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class YourVehiclesController extends AbstractController
{
    #[Route('/driver/your-vehicles', name: 'app_your_vehicles')]
    #[IsGranted('ROLE_DRIVER')]
    public function index(
        Request $request,
        VehicleRepository $vehicleRepository,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
    ): Response {
        $user = $this->getUser();

        $vehicles = $vehicleRepository->findBy([
            'owner' => $user,
        ]);

        $data = new VehicleData();
        $form = $this->createForm(VehicleType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle = new Vehicle();
            $vehicle->setOwner($user);
            $vehicle->setPlate($data->plate);
            $vehicle->setColor($data->color);
            $vehicle->setFirstRegistrationDate($data->firstRegistrationDate);
            $vehicle->setCarModel($data->model);
            $vehicle->setIsArchived(false);

            $errors = $validator->validate($vehicle);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    if ($error->getPropertyPath() === 'plate') {
                        $form->get('plate')->addError(new FormError($error->getMessage()));
                    }
                }
                return $this->render('your_vehicles/index.html.twig', [
                    'vehicleForm' => $form->createView(),
                    'user' => $user,
                    'vehicles' => $vehicles,
                ]);
            }


            $em->persist($vehicle);
            $em->flush();

            $this->addFlash('success', 'Véhicule ajouté avec succès.');
            return $this->redirectToRoute('app_your_vehicles');
        }

        return $this->render('your_vehicles/index.html.twig', [
            'user' => $user,
            'vehicles' => $vehicles,
            'vehicleForm' => $form->createView(),
        ]);
    }
}
