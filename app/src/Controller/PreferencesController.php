<?php

namespace App\Controller;

use App\Document\DriverPreference;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PreferencesController extends AbstractController
{
    #[Route('/driver/dashboard/preferences', name: 'app_driver_preferences')]
    #[IsGranted('ROLE_DRIVER')]
    public function preferences(
        DocumentManager $dm,
    ): Response {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */
        $preferences = $dm->getRepository(DriverPreference::class)->findOneBy(['userId' => $user->getId()]);
        return $this->render('dashboard/driver/preferences.html.twig', [
            'preferences' => $preferences,
        ]);
    }
    #[Route('/driver/dashboard/preferences/update', name: 'app_driver_preferences_update', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function updatePreferences(
        Request $request,
        DocumentManager $dm
    ): Response {
        $user = $this->getUser();

        if (!$this->isCsrfTokenValid('update_preferences', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide');
        }

        /** @var \App\Entity\User $user */
        $preference = $dm->getRepository(\App\Document\DriverPreference::class)->findOneBy(['userId' => $user->getId()]);
        if (!$preference) {
            $preference = new \App\Document\DriverPreference();
            $preference->setUserId($user->getId());
        }

        $preference->setMusicAllowed($request->request->has('musicAllowed'));
        $preference->setSmokingAllowed($request->request->has('smokingAllowed'));
        $preference->setAnimalsAllowed($request->request->has('animalsAllowed'));

        $dm->persist($preference);
        $dm->flush();

        $this->addFlash('success', 'Préférences mises à jour avec succès.');

        return $this->redirectToRoute('app_driver_preferences');
    }

    #[Route('/driver/dashboard/preferences/custom-add', name: 'app_driver_preferences_custom_add', methods: ['POST'])]
    public function addCustomPreference(Request $request, DocumentManager $dm): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */

        if (!$this->isCsrfTokenValid('add_custom_preference', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide');
        }

        $preference = $dm->getRepository(DriverPreference::class)->findOneBy(['userId' => $user->getId()]);
        if (!$preference) {
            $preference = new DriverPreference();
            $preference->setUserId($user->getId());
        }

        $newPref = trim($request->request->get('new_preference'));
        if ($newPref) {
            $preference->addCustomPreference($newPref);
            $dm->persist($preference);
            $dm->flush();
            $this->addFlash('success', 'Préférence ajoutée.');
        }

        return $this->redirectToRoute('app_driver_preferences');
    }

    #[Route('/driver/dashboard/preferences/custom-remove', name: 'app_driver_preferences_custom_remove', methods: ['POST'])]
    public function removeCustomPreference(Request $request, DocumentManager $dm): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */

        if (!$this->isCsrfTokenValid('remove_custom_preference', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide');
        }

        $preference = $dm->getRepository(DriverPreference::class)->findOneBy(['userId' => $user->getId()]);
        if ($preference) {
            $preference->removeCustomPreference($request->request->get('preference'));
            $dm->flush();
            $this->addFlash('success', 'Préférence supprimée.');
        }

        return $this->redirectToRoute('app_driver_preferences');
    }
}
