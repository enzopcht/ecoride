<?php

namespace App\Controller\dashboard;

use App\Entity\CreditTransaction;
use App\Entity\User;
use App\Form\EmployeType;
use App\Form\RegistrationType;
use App\Repository\CreditTransactionRepository;
use App\Repository\RideRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(
        CreditTransactionRepository $creditTransactionRepository,
        RideRepository $rideRepository,
        Request $request
    ): Response {
        $rangeRides = $request->query->getInt('rangeRides', 7);
        $rangeCredits = $request->query->getInt('rangeCredits', 7);

        $ridesPerDay = $rideRepository->countRidesGroupedByDay($rangeRides);
        $labelsRide = array_column($ridesPerDay, 'date');
        $dataRide = array_column($ridesPerDay, 'count');

        $creditsPerDay = $creditTransactionRepository->countCreditsGroupedByDay($rangeCredits);
        $labelsCredit = array_column($creditsPerDay, 'date');
        $dataCredit = array_column($creditsPerDay, 'amount');
        // dd($creditsPerDay);

        $commissions = $creditTransactionRepository->findBy([
            'reason' => 'Commission'
        ]);
        $refundCommissions = $creditTransactionRepository->findBy([
            'reason' => 'Refund Commission'
        ]);
        $balance = (count($commissions) * 2) - (count($refundCommissions) * 2);
        return $this->render('dashboard/admin/admin.html.twig', [
            'user' => $this->getUser(),
            'balance' => $balance,
            'rideLabels' => $labelsRide,
            'rideCounts' => $dataRide,
            'creditLabels' => $labelsCredit,
            'creditCounts' => $dataCredit,
        ]);
    }

    #[Route('/admin/dashboard/create-employee', name: 'app_create_employee')]
    #[IsGranted('ROLE_ADMIN')]
    public function createEmployee(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_EMPLOYE']);
            $user->setSuspended(false);

            $em->persist($user);

            $em->flush();

            $this->addFlash('primary', 'Le compte employé a bien été créé.');
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('dashboard/admin/create-employee.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/dashboard/manage-accounts', name: 'app_manage_accounts')]
    #[IsGranted('ROLE_ADMIN')]
    public function manageAccounts(
        UserRepository $userRepository,
        Request $request,
        PaginatorInterface $paginator,
    ): Response {
        $sortUsers = $request->query->get('sortUsers', 'id');
        $directionUsers = $request->query->get('directionUsers', 'asc');

        $sortEmployes = $request->query->get('sortEmployes', 'id');
        $directionEmployes = $request->query->get('directionEmployes', 'asc');

        $searchUsers = $request->query->get('searchUsers', '');
        $searchEmployes = $request->query->get('searchEmployes', '');

        $allowedSorts = ['id', 'pseudo', 'email', 'roles', 'suspended'];
        if (!in_array($sortUsers, $allowedSorts)) $sortUsers = 'id';
        if (!in_array($sortEmployes, $allowedSorts)) $sortEmployes = 'id';

        $queryUsers = $userRepository->createQueryBuilder('u')
            ->where('(u.roles LIKE :role1 OR u.roles LIKE :role2)')
            ->setParameter('role1', '%ROLE_PASSENGER%')
            ->setParameter('role2', '%ROLE_DRIVER%');

        if ($searchUsers !== '') {
            $queryUsers->andWhere('u.pseudo LIKE :searchUsers OR u.email LIKE :searchUsers')
                ->setParameter('searchUsers', '%' . $searchUsers . '%');
        }

        $queryUsers->orderBy('u.' . $sortUsers, $directionUsers);

        $queryEmployes = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_EMPLOYE%');

        if ($searchEmployes !== '') {
            $queryEmployes->andWhere('u.pseudo LIKE :searchEmployes OR u.email LIKE :searchEmployes')
                ->setParameter('searchEmployes', '%' . $searchEmployes . '%');
        }

        $queryEmployes->orderBy('u.' . $sortEmployes, $directionEmployes);

        $users = $paginator->paginate($queryUsers, $request->query->getInt('pageUsers', 1), 5);
        $employes = $paginator->paginate($queryEmployes, $request->query->getInt('pageEmployes', 1), 5);

        return $this->render('dashboard/admin/manage-accounts.html.twig', [
            'users' => $users,
            'employes' => $employes,
            'sortUsers' => $sortUsers,
            'directionUsers' => $directionUsers,
            'sortEmployes' => $sortEmployes,
            'directionEmployes' => $directionEmployes,
            'searchUsers' => $searchUsers,
            'searchEmployes' => $searchEmployes,
        ]);
    }
}
