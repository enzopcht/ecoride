<?php

namespace App\Controller;

use App\Entity\Ride;
use App\Repository\CreditTransactionRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ride', name: 'ride_')]
class RideController extends AbstractController
{
    #[Route('/{id}/complete', name: 'complete', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function completeRide(
        Ride $ride,
        ParticipationRepository $participationRepository,
        EntityManagerInterface $em,
        Request $request,
        MailerInterface $mailer
    ): RedirectResponse {
        $user = $this->getUser();

        if ($ride->getDriver() !== $user || $ride->getStatus() !== 'active') {
            throw $this->createAccessDeniedException();
        }


        $ride->setStatus('completed');


        $participations = $participationRepository->findBy([
            'ride' => $ride,
            'status' => 'active',
        ]);

        foreach ($participations as $participation) {
            $participation->setStatus('waiting_passenger_review');

            $email = (new Email())
                ->from($this->getParameter('no-reply@ecoride.fr'))
                ->to($participation->getUser()->getEmail())
                ->subject('Votre trajet est terminé – Confirmez son bon déroulé !')
                ->html($this->renderView('emails/review_request.html.twig', [
                    'user' => $participation->getUser(),
                    'ride' => $ride,
                ]));

            $mailer->send($email);
        }

        $em->flush();

        $this->addFlash('success', 'Trajet terminé. Vos passagers doivent maintenant confirmer le trajet.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function deleteRide(
        Ride $ride,
        ParticipationRepository $participationRepository,
        EntityManagerInterface $em,
        CreditTransactionRepository $creditTransactionRepository,
        Request $request
    ): RedirectResponse {
        $user = $this->getUser();

        if ($ride->getDriver() !== $user || $ride->getStatus() !== 'pending') {
            throw $this->createAccessDeniedException();
        }

        $ride->setStatus('canceled');


        $participations = $participationRepository->findBy([
            'ride' => $ride,
            'status' => ['pending', 'confirmed'],
        ]);

        if ($participations) {
            foreach ($participations as $participation) {
                $participation->setStatus('rejected');
                $creditTransactionRepository->createTransaction($participation->getUser(), $ride, $ride->getPrice() - 2, 'Refund');
                $creditTransactionRepository->createTransaction($participation->getUser(), $ride, 2, 'Refund Commission');
            }
        }

        $em->flush();

        $this->addFlash('success', 'Trajet annulé. Les remboursements nécessaires vont être fait.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/{id}/start', name: 'start', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function startRide(
        Ride $ride,
        EntityManagerInterface $em,
        ParticipationRepository $participationRepository,
        CreditTransactionRepository $creditTransactionRepository,
        Request $request
    ): RedirectResponse {
        $user = $this->getUser();

        if ($ride->getDriver() !== $user || $ride->getStatus() !== 'pending') {
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

                $creditTransactionRepository->createTransaction($participation->getUser(), $ride, $ride->getPrice() - 2, 'Refund');
                $creditTransactionRepository->createTransaction($participation->getUser(), $ride, 2, 'Refund Commission');
            }
        }

        $em->flush();

        $this->addFlash('success', 'Trajet démarré. Merci de confirmer l\'arriver de celui-ci dans votre tableau de bord à la fin de votre trajet.');

        return $this->redirect($request->headers->get('referer'));
    }
}
