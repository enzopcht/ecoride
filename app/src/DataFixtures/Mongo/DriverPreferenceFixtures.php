<?php

namespace App\DataFixtures\Mongo;

use App\Document\DriverPreference;
use App\Entity\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ManagerRegistry;


class DriverPreferenceFixtures extends Fixture
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function load(\Doctrine\Persistence\ObjectManager $manager): void
    {
        /** @var DocumentManager $manager */
        $entityManager = $this->registry->getManager();
        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $roles = $user->getRoles();
            if (in_array('ROLE_ADMIN', $roles, true) || in_array('ROLE_EMPLOYE', $roles, true)) {
                continue;
            }

            $pref = new DriverPreference();
            $pref->setUserId($user->getId())
                ->setMusicAllowed((bool) random_int(0, 1))
                ->setSmokingAllowed((bool) random_int(0, 1))
                ->setAnimalsAllowed((bool) random_int(0, 1));

            $manager->persist($pref);
        }

        $manager->flush();
    }

    
}
