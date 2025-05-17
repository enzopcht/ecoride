<?php

namespace App\Controller;

use App\Entity\Report;
use App\Repository\CreditTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reports', name: 'app_reports_')]
class ReportsController extends AbstractController
{
    #[Route('/refund/{id}', name: 'refund')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function refundReport(
        Request $request,
        Report $report,
        EntityManagerInterface $em,
        CreditTransactionRepository $creditTransactionRepository,
    ): RedirectResponse
    {
        if ($report->getStatus() !== 'pending') {
            $this->addFlash('warning', 'Ce litige a déjà été traité.');
            return $this->redirectToRoute('referer');
        }
        $participation = $report->getParticipation();
        $participation->setStatus('resolved_refund');
        $em->persist($participation);

        $report->setStatus('resolved_refund');
        $em->persist($report);

        $ride = $participation->getRide();
        $creditTransactionRepository->createTransaction($participation->getUser(), $ride, $ride->getPrice() - 2, 'Refund');
        $creditTransactionRepository->createTransaction($participation->getUser(), $ride, 2, 'Commission');

        $em->flush();

        $this->addFlash('primary', 'Vous avez régulé la situation, le passager a été remboursé.');

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/decline//{id}', name: 'decline')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function declineReport(
        Request $request,
        Report $report,
        EntityManagerInterface $em,
        CreditTransactionRepository $creditTransactionRepository,
    ): RedirectResponse
    {
        if ($report->getStatus() !== 'pending') {
            $this->addFlash('warning', 'Ce litige a déjà été traité.');
            return $this->redirectToRoute('referer');
        }
        $participation = $report->getParticipation();
        $participation->setStatus('resolved');
        $em->persist($participation);

        $report->setStatus('resolved');
        $em->persist($report);

        $ride = $participation->getRide();
        $creditTransactionRepository->createTransaction($ride->getDriver(), $ride, $ride->getPrice() - 2, 'Driver payment');

        $em->flush();

        $this->addFlash('primary', 'Vous avez régulé la situation, le conducteur a été payé.');

        return $this->redirect($request->headers->get('referer'));
    }
}
