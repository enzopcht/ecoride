<?php

namespace App\Controller;

use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participations', name: 'participations_')]
class ParticipationController extends AbstractController
{
    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Participation $participation, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if ($participation->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($participation);
        $em->flush();

        $this->addFlash('success', 'Votre réservation a bien été annulée.');

        return $this->redirectToRoute('app_dashboard_passager');
    }
}
