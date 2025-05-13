<?php

namespace App\Controller;

use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/participations', name: 'participations_')]
class ParticipationController extends AbstractController
{
    #[Route('/canceled/{id}', name: 'canceled', methods: ['POST'])]
    #[IsGranted(['ROLE_PASSENGER','ROLE_DRIVER'])]
    public function canceled(
        Participation $participation,
        EntityManagerInterface $em
        ): RedirectResponse
    {
        $user = $this->getUser();
        if ($participation->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $participation->setStatus('canceled');
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été annulée.');

        return $this->redirectToRoute('app_dashboard');
    }
    #[Route('/accept/{id}', name: 'accept', methods: ['POST'])]
    public function accept(Participation $participation, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if ($participation->getRide()->getDriver() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $participation->setStatus('confirmed');
        $em->flush();

        $this->addFlash('success', 'La réservation a été acceptée.');

        return $this->redirectToRoute('app_driver_manage_your_bookings');
    }
    #[Route('/reject/{id}', name: 'reject', methods: ['POST'])]
    public function reject(Participation $participation, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if ($participation->getRide()->getDriver() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $participation->setStatus('rejected');
        $em->flush();

        $this->addFlash('danger', 'La réservation a été rejetée.');

        return $this->redirectToRoute('app_driver_manage_your_bookings');
    }
}
