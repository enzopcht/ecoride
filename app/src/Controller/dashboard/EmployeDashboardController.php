<?php

namespace App\Controller\dashboard;

use App\Repository\ReportRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EmployeDashboardController extends AbstractController
{
    #[Route('/employe/dashboard', name: 'app_dashboard_employe')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function employe(
        ReviewRepository $reviewRepository,
        ReportRepository $reportRepository,
    ): Response
    {
        $reviews = $reviewRepository->findBy([
            'validated' => false,
        ]);

        $reports = $reportRepository->findBy([
            'status' => 'pending',
        ]);

        return $this->render('dashboard/employe/employe.html.twig', [
            'user' => $this->getUser(),
            'reviews' => $reviews,
            'reports' => $reports,
        ]);
    }
    #[Route('/employe/dashboard/manage-reviews', name: 'app_manage_reviews')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function manageReviews(
        ReviewRepository $reviewRepository,
    ): Response
    {
        $reviews = $reviewRepository->findBy([
            'validated' => false,
        ]);

        return $this->render('dashboard/employe/manage-reviews.html.twig', [
            'user' => $this->getUser(),
            'reviews' => $reviews,
        ]);
    }
    #[Route('/employe/dashboard/manage-reports', name: 'app_manage_reports')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function manageReports(
        ReportRepository $reportRepository,
    ): Response
    {
        $reports = $reportRepository->findBy([
            'status' => 'pending',
        ]);

        return $this->render('dashboard/employe/manage-reports.html.twig', [
            'user' => $this->getUser(),
            'reports' => $reports,
        ]);
    }
}