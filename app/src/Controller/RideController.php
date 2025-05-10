<?php

namespace App\Controller;

use App\Entity\Ride;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ride', name: 'ride_')]
class RideController extends AbstractController
{
    #[Route('/ride/{id}/complete', name: 'complete', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function completeRide(
        Ride $ride,
        ParticipationRepository $participationRepository,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if ($ride->getDriver() !== $user) {
            throw $this->createAccessDeniedException();
        }

        
        $ride->setStatus('completed');

        
        $participations = $participationRepository->findBy([
            'ride' => $ride,
            'status' => 'confirmed',
        ]);

        foreach ($participations as $participation) {
            $participation->setStatus('waiting_passenger_review');
        }

        $em->flush();

        $this->addFlash('success', 'Trajet terminé. Vos passagers doivent maintenant confirmer le trajet.');

        return $this->redirectToRoute('app_dashboard_driver');
    }
}
