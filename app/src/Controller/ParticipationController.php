<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\Report;
use App\Entity\Ride;
use App\Repository\CreditTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participations', name: 'participations_')]
class ParticipationController extends AbstractController
{
    #[Route('/book/{id}', name: 'book', methods: ['POST'])]
    public function book(
        Request $request,
        Ride $ride,
        EntityManagerInterface $em,
        CreditTransactionRepository $transactionRepository,
    ): RedirectResponse {
        $user = $this->getUser();

        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour réserver un trajet.');
            return $this->redirectToRoute('app_login');
        }

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }
        if ($ride->getDriver() === $user) {
            $this->addFlash('danger', 'Vous ne pouvez pas réserver votre propre trajet.');
            return $this->redirect($request->headers->get('referer'));
        }
        if ($ride->getSeatsAvailable() <= 0) {
            $this->addFlash('danger', 'Ce trajet est complet.');
            return $this->redirect($request->headers->get('referer'));
        }
        if ($ride->getStatus() !== 'pending') {
            $this->addFlash('danger', 'Ce trajet n\'est plus réservable.');
            return $this->redirect($request->headers->get('referer'));
        }
        $balance = $transactionRepository->calculateUserBalance($this->getUser());
        if ($ride->getPrice() > $balance) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de jetons.');
            return $this->redirect($request->headers->get('referer'));
        }

        $transactionRepository->createTransaction($user, $ride, - ($ride->getPrice() - 2), 'Booking a trip');
        $transactionRepository->createTransaction($user, $ride, -2, 'Commission');

        $participation = new Participation();
        $participation->setUser($user);
        $participation->setRide($ride);
        $participation->setStatus('pending');
        $participation->setCreditsUsed($ride->getPrice());

        $em->persist($participation);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été prise en compte, Le chauffeur doit la valider.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/canceled/{id}', name: 'canceled', methods: ['POST'])]
    public function canceled(
        Request $request,
        Participation $participation,
        CreditTransactionRepository $creditTransactionRepository,
        EntityManagerInterface $em
    ): RedirectResponse {
        $user = $this->getUser();

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }

        if (
            $participation->getUser() !== $user ||
            $participation->getRide()->getStatus() !== 'pending' ||
            !in_array($participation->getStatus(), ['pending', 'confirmed'], true)
        ) {
            throw $this->createAccessDeniedException();
        }

        $ride = $participation->getRide();

        if ($participation->getStatus() === 'confirmed') {
            $ride->setSeatsAvailable($ride->getSeatsAvailable() + 1);
        }

        $participation->setStatus('canceled');


        $creditTransactionRepository->createTransaction($participation->getUser(), $ride, $ride->getPrice() - 2, 'Refund');
        $creditTransactionRepository->createTransaction($participation->getUser(), $ride, 2, 'Refund Commission');
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été annulée.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/accept/{id}', name: 'accept', methods: ['POST'])]
    public function accept(
        Participation $participation,
        EntityManagerInterface $em,
        Request $request,
    ): RedirectResponse {
        $user = $this->getUser();

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($participation->getRide()->getDriver() !== $user ||  $participation->getStatus() !== 'pending' || $participation->getRide()->getStatus() !== 'pending') {
            throw $this->createAccessDeniedException();
        }

        $ride = $participation->getRide();
        $participation->setStatus('confirmed');
        if ($ride->getSeatsAvailable() > 0) {
            $ride->setSeatsAvailable($ride->getSeatsAvailable() - 1);
        } else {
            $this->addFlash('danger', 'Vous n\'avez plus de place disponible sur ce trajet. cette réservation a été refusée');
            return $this->redirect($request->headers->get('referer'));
        }
        $em->flush();

        $this->addFlash('success', 'La réservation a été acceptée.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/reject/{id}', name: 'reject', methods: ['POST'])]
    public function reject(
        Participation $participation,
        EntityManagerInterface $em,
        CreditTransactionRepository $creditTransactionRepository,
        Request $request,
    ): RedirectResponse {
        $user = $this->getUser();

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($participation->getRide()->getDriver() !== $user || $participation->getStatus() !== 'pending' || $participation->getRide()->getStatus() !== 'pending') {
            throw $this->createAccessDeniedException();
        }

        $ride = $participation->getRide();
        $participation->setStatus('rejected');

        $creditTransactionRepository->createTransaction($participation->getUser(), $ride, $ride->getPrice() - 2, 'Refund');
        $creditTransactionRepository->createTransaction($participation->getUser(), $ride, 2, 'Refund Commission');

        $em->flush();

        $this->addFlash('danger', 'La demande de réservation a été rejetée.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/valid-ride/{id}', name: 'valid_ride', methods: ['POST'])]
    public function validRide(
        Participation $participation,
        EntityManagerInterface $em,
        CreditTransactionRepository $creditTransactionRepository,
        Request $request,
    ): RedirectResponse {
        $user = $this->getUser();

        if (
            !$this->isGranted('ROLE_PASSENGER') &&
            !$this->isGranted('ROLE_DRIVER')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($participation->getUser() !== $user || $participation->getStatus() !== 'waiting_passenger_review' || $participation->getRide()->getStatus() !== 'completed') {
            throw $this->createAccessDeniedException();
        }

        $participation->setStatus('validated');

        $ride = $participation->getRide();

        $creditTransactionRepository->createTransaction($ride->getDriver(), $ride, $ride->getPrice() - 2, 'Driver payment');

        $em->flush();

        $this->addFlash('success', 'Ravi que tout se soit bien passé ! Merci d\'avoir utilisé EcoRide. Vous pouvez laisser un avis au conducteur.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/report/{id}', name: 'report', methods: ['POST'])]
    public function report(
        Participation $participation,
        EntityManagerInterface $em,
        Request $request,
    ): RedirectResponse {
        $user = $this->getUser();

        if ($participation->getUser() !== $user || $participation->getStatus() !== 'waiting_passenger_review' || $participation->getRide()->getStatus() !== 'completed') {
            throw $this->createAccessDeniedException();
        }

        
        $description = $request->request->get('description');

        if (!$description) {
            $this->addFlash('danger', 'Vous devez fournir une description du problème.');
            return $this->redirect($request->headers->get('referer'));
        }

        $report = new Report();
        $report->setAuthor($user);
        $report->setParticipation($participation);
        $report->setDescription($description);
        $report->setCreatedAt(new \DateTimeImmutable());
        $report->setStatus('pending');

        $participation->setStatus('disputed');
        $em->persist($report);
        $em->flush();

        $this->addFlash('warning', 'Votre signalement a bien été envoyé, un employé va prendre en main la situation et revenir vers vous.');

        return $this->redirect($request->headers->get('referer'));
    }
}
