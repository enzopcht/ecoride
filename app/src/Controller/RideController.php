<?php

namespace App\Controller;

use App\Entity\CreditTransaction;
use App\Entity\Ride;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
        EntityManagerInterface $em,
        Request $request
    ): RedirectResponse {
        $user = $this->getUser();

        if ($ride->getDriver() !== $user) {
            throw $this->createAccessDeniedException();
        }


        $ride->setStatus('completed');


        $participations = $participationRepository->findBy([
            'ride' => $ride,
            'status' => 'active',
        ]);

        foreach ($participations as $participation) {
            $participation->setStatus('waiting_passenger_review');
        }

        $em->flush();

        $this->addFlash('success', 'Trajet terminé. Vos passagers doivent maintenant confirmer le trajet.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/ride/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function deleteRide(
        Ride $ride,
        ParticipationRepository $participationRepository,
        EntityManagerInterface $em,
        Request $request
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

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/ride/{id}/start', name: 'start', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function startRide(
        Ride $ride,
        EntityManagerInterface $em,
        ParticipationRepository $participationRepository,
        Request $request
    ): RedirectResponse {
        $user = $this->getUser();

        if ($ride->getDriver() !== $user) {
            throw $this->createAccessDeniedException();
        }
        $ride->setStatus('active');
        $participations = $participationRepository->findBy([
            'ride' => $ride,
            'status' => ['confirmed', 'pending'],
        ]);

        foreach ($participations as $participation) {
            if ($participation->getStatus() === 'confirmed') {
                $participation->setStatus('active');
            } else {
                $participation->setStatus('rejected');

                $transactionRefund = new CreditTransaction();
                $transactionRefund->setUser($participation->getUser());
                $transactionRefund->setRide($ride);
                $transactionRefund->setAmount($ride->getPrice() - 2);
                $transactionRefund->setReason('Refund');
                $transactionRefund->setCreatedAt(new \DateTimeImmutable());

                $em->persist($transactionRefund);

                $commissionRefund = new CreditTransaction();
                $commissionRefund->setUser($participation->getUser());
                $commissionRefund->setRide($ride);
                $commissionRefund->setAmount(2);
                $commissionRefund->setReason('Commission');
                $commissionRefund->setCreatedAt(new \DateTimeImmutable());

                $em->persist($commissionRefund);
            }
        }

        $em->flush();

        $this->addFlash('success', 'Trajet démarré. Merci de confirmer l\'arriver de celui-ci dans votre tableau de bord à la fin de votre trajet.');

        return $this->redirect($request->headers->get('referer'));
    }
}
