<?php

namespace App\Controller;

use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participations', name: 'participations_')]
class ParticipationController extends AbstractController
{
    #[Route('/canceled/{id}', name: 'canceled', methods: ['POST'])]
    public function canceled(
        Request $request,
        Participation $participation,
        EntityManagerInterface $em
        ): RedirectResponse
    {
        $user = $this->getUser();

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($participation->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $participation->setStatus('canceled');
        //rembourser
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été annulée.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/accept/{id}', name: 'accept', methods: ['POST'])]
    public function accept(
        Participation $participation,
        EntityManagerInterface $em,
        Request $request,
        ): RedirectResponse
    {
        $user = $this->getUser();

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($participation->getRide()->getDriver() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $participation->setStatus('confirmed');
        $em->flush();

        $this->addFlash('success', 'La réservation a été acceptée.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/reject/{id}', name: 'reject', methods: ['POST'])]
    public function reject(
        Participation $participation,
        EntityManagerInterface $em,
        Request $request,
        ): RedirectResponse
    {
        $user = $this->getUser();

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($participation->getRide()->getDriver() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $participation->setStatus('rejected');
        //rembourser
        $em->flush();

        $this->addFlash('danger', 'La demande de réservation a été rejetée.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/valid-ride/{id}', name: 'valid_ride', methods: ['POST'])]
    public function validRide(
        Participation $participation,
        EntityManagerInterface $em,
        Request $request,
        ): RedirectResponse
    {
        $user = $this->getUser();

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($participation->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $participation->setStatus('validated');
        //déclencher paiement
        $em->flush();

        $this->addFlash('success', 'Ravi que tout se soit bien passé ! Merci d\'avoir utilisé EcoRide.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/report/{id}', name: 'report', methods: ['POST'])]
    public function report(
        Participation $participation,
        EntityManagerInterface $em,
        Request $request,
        ): RedirectResponse
    {
        $user = $this->getUser();
        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($participation->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $participation->setStatus('disputed');
        // Faire la révocation
        $em->flush();

        $this->addFlash('primary', 'Désolé d\'apprendre qu\'il y a eu un soucis sur votre trajet. Un membre de notre équipe va prendre en main votre problème rapidement.');

        return $this->redirect($request->headers->get('referer'));
    }
}
