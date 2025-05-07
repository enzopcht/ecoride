<?php

namespace App\DataFixtures\Mongo;

use App\Document\DriverPreference;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ManagerRegistry;


class DriverPreferenceFixtures extends Fixture
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function load(ObjectManager $manager): void
    {
        $entityManager = $this->registry->getManager();
        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
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
