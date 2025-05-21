<?php

namespace App\Controller;

use App\Form\AddRideType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AddRideController extends AbstractController
{
    #[Route('/add/ride', name: 'app_add_ride')]
    #[IsGranted('ROLE_DRIVER')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
    ): Response
    {
        $form = $this->createForm(AddRideType::class, null, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            /**
             * @var \App\Entity\User $user
             */
            $ride = $form->getData();
            $ride->setDriver($user);
            if ($ride->getVehicle()->getCarModel()->getEnergy() == 'electric') {
                $ride->setEcological(true);
            } else {
                $ride->setEcological(false);
            }
            $ride->setStatus('pending');
            dd($ride);

            $em->persist($ride);
            $em->flush();

            $this->addFlash('success', 'Trajet créé avec succès.');

            return $this->redirectToRoute('app_dashboard_driver');

            
        }
        return $this->render('add_ride/index.html.twig', [
            'addRideForm' => $form->createView(),
            'ors_api_key' => $_ENV['ORS_API_KEY'],
        ]);
    }
}
