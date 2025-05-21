<?php

namespace App\Controller;

use App\Document\DriverPreference;
use App\Form\BecomeDriverType;
use App\Form\Model\BecomeDriverData;
use App\Security\LoginAuthenticator;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BecomeDriverController extends AbstractController
{
    #[Route('/passenger/become-driver', name: 'become_driver')]
    #[IsGranted('ROLE_PASSENGER')]

    public function index(
        Request $request,
        EntityManagerInterface $em,
        DocumentManager $dm,
        UserAuthenticatorInterface $userAuthenticator,
        LoginAuthenticator $authenticator,
        ValidatorInterface $validator
    ): Response {
        $data = new BecomeDriverData();
        $form = $this->createForm(BecomeDriverType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            /**
             * @var \App\Entity\User $user
             */
            $user->setRoles(['ROLE_DRIVER']);

            $vehicleData = $data->vehicle;

            $vehicle = new \App\Entity\Vehicle();
            $vehicle->setOwner($user);
            $vehicle->setPlate($vehicleData->plate);
            $vehicle->setFirstRegistrationDate($vehicleData->firstRegistrationDate);
            $vehicle->setCarModel($vehicleData->model);
            $vehicle->setColor($vehicleData->color);

            $errors = $validator->validate($vehicle);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    if ($error->getPropertyPath() === 'plate') {
                        $form->get('vehicle')->get('plate')->addError(new FormError($error->getMessage()));
                    }
                }
                return $this->render('become_driver/index.html.twig', [
                    'becomeDriverForm' => $form->createView(),
                ]);
            }

            $em->persist($vehicle);

            $preference = $dm->getRepository(DriverPreference::class)->findOneBy(['userId' => $user->getId()]);
            if (!$preference) {
                $preference = new DriverPreference();
                $preference->setUserId($user->getId());
            }

            $preference->setMusicAllowed($data->musicAllowed);
            $preference->setSmokingAllowed($data->smokingAllowed);
            $preference->setAnimalsAllowed($data->animalsAllowed);

            $dm->persist($preference);

            $em->flush();
            $dm->flush();
            $this->addFlash('success', 'Vous êtes maintenant un Driver.');
            $userAuthenticator->authenticateUser($user, $authenticator, $request);
            return $this->redirectToRoute('app_dashboard_driver');
        }

        return $this->render('become_driver/index.html.twig', [
            'becomeDriverForm' => $form->createView(),
        ]);
    }
}
