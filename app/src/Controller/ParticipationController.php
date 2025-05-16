<?php

namespace App\Controller;

use App\Entity\CreditTransaction;
use App\Entity\Participation;
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
        ): RedirectResponse
    {
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
        $balance = $transactionRepository->calculateUserBalance($this->getUser());
        if ($ride->getPrice() > $balance) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de jetons.');
            return $this->redirect($request->headers->get('referer'));
        }
        
        $transaction = new CreditTransaction();
        $transaction->setUser($user);
        $transaction->setRide($ride);
        $transaction->setAmount(-($ride->getPrice() - 2));
        $transaction->setReason('Booking a trip');
        $transaction->setCreatedAt(new \DateTimeImmutable());
        
        $em->persist($transaction);
        
        $transactionCommission = new CreditTransaction();
        $transactionCommission->setUser($user);
        $transactionCommission->setRide($ride);
        $transactionCommission->setAmount(-2);
        $transactionCommission->setReason('Commmission EcoRide');
        $transactionCommission->setCreatedAt(new \DateTimeImmutable());

        $em->persist($transactionCommission);

        $participation = new Participation();
        $participation->setUser($user);
        $participation->setRide($ride);
        $participation->setStatus('pending');
        $participation->setCreditsUsed($ride->getPrice());

        $ride->setSeatsAvailable($ride->getSeatsAvailable() - 1);
        
        $em->persist($participation);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été prise en compte, retrouvez là dans vos réservations.');

        return $this->redirect($request->headers->get('referer'));
    }
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
        
        $ride = $participation->getRide();

        $transaction = new CreditTransaction();
        $transaction->setUser($ride->getDriver());
        $transaction->setRide($ride);
        $transaction->setAmount($ride->getPrice() - 2);
        $transaction->setReason('Driver payment');
        $transaction->setCreatedAt(new \DateTimeImmutable());
        
        $em->persist($transaction);
        
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
