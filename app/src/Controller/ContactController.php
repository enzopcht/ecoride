<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        MailerInterface $mailer,
    ): Response {
        $form = $this->createForm(ContactType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $email = (new Email())
                ->from($formData['email'])
                ->to('contact.team.ecoride@gmail.com')
                ->subject('Nouveau message de contact')
                ->text(
                    "Nom : {$formData['first_name']} {$formData['last_name']}\n" .
                    "Email : {$formData['email']}\n\n" .
                    $formData['message']
                );

            $mailer->send($email);
            $this->addFlash('success', 'Votre message a bien été envoyé.');
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
