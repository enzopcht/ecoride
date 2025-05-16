<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ReviewController extends AbstractController
{

    #[Route('/review/{id}', name: 'app_review')]
    public function index(
        Request $request,
        Participation $participation,
        EntityManagerInterface $em,
    ): Response {
        $review = new Review();
        $review->setAuthor($this->getUser());
        $review->setTarget($participation->getRide()->getDriver());
        $review->setRide($participation->getRide());
        $review->setCreatedAt(new \DateTimeImmutable());
        $review->setValidated(false);

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'Merci, votre avis à bien été pris en compte et va être soumis à validation par un employé.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('review/index.html.twig', [
            'reviewForm' => $form->createView(),
            'participation' => $participation,
        ]);
    }

    #[Route('/employe/valid-review/{id}', name: 'app_valid_review', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function validReview(
        Review $review,
        EntityManagerInterface $em,
        Request $request
    ): RedirectResponse {
        $employe = $this->getUser();

        $review->setValidated(true);

        $em->flush();

        $this->addFlash('primary', 'L\'avis a bien été validé.');

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/employe/reject-review/{id}', name: 'app_reject_review', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYE')]
    public function rejectReview(
        Review $review,
        EntityManagerInterface $em,
        Request $request
    ): RedirectResponse {
        $employe = $this->getUser();

        $em->remove($review);

        $em->flush();

        $this->addFlash('primary', 'L\'avis a bien été supprimé.');

        return $this->redirect($request->headers->get('referer'));
    }
}
