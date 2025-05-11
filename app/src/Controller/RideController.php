<?php

namespace App\Controller;

use App\Entity\Ride;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    ): RedirectResponse {
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
    #[Route('/ride/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function deleteRide(
        Ride $ride,
        ParticipationRepository $participationRepository,
        EntityManagerInterface $em
    ): RedirectResponse {
        $user = $this->getUser();

        if ($ride->getDriver() !== $user) {
            throw $this->createAccessDeniedException();
        }

        
        $ride->setStatus('canceled');

        
        $participations = $participationRepository->findBy([
            'ride' => $ride,
            'status' => ['pending', 'confirmed'],
        ]);

        foreach ($participations as $participation) {
            $participation->setStatus('canceled');
        }

        $em->flush();

        $this->addFlash('success', 'Trajet annulé. Les remboursements nécessaires vont être fait.');

        return $this->redirectToRoute('app_dashboard_driver');
    }
    #[Route('/ride/{id}/start', name: 'start', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function startRide(
        Ride $ride,
        EntityManagerInterface $em
    ): RedirectResponse {
        $user = $this->getUser();

        if ($ride->getDriver() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $ride->setStatus('active');

        $em->flush();

        $this->addFlash('success', 'Trajet démarré. Merci de confirmer l\'arriver de celui-ci dans votre tableau de bord à la fin de votre trajet.');

        return $this->redirectToRoute('app_dashboard_driver');
    }
}
