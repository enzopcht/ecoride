<?php

namespace App\Controller;

use App\Form\BecomeDriverType;
use App\Form\Model\BecomeDriverData;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class BecomeDriverController extends AbstractController
{
    #[Route('/become-driver', name: 'become_driver')]
    #[IsGranted('ROLE_PASSENGER')]
    public function index(Request $request, EntityManagerInterface $em, DocumentManager $dm): Response
    {
        $data = new BecomeDriverData();
        $form = $this->createForm(BecomeDriverType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $this->addFlash('success', 'Vous êtes maintenant conducteur.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('become_driver/index.html.twig', [
            'becomeDriverForm' => $form->createView(),
        ]);
    }
}
