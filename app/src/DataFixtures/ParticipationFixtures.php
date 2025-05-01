<?php

namespace App\DataFixtures;

use App\Entity\Participation;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ParticipationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $rides = $manager->getRepository(Ride::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($rides as $ride) {
            $availableSeats = $ride->getSeatsAvailable();
            $possibleUsers = array_filter($users, fn($u) => $u !== $ride->getDriver());

            $participants = $faker->randomElements($possibleUsers, min($availableSeats, $faker->numberBetween(0, 3)));

            foreach ($participants as $user) {
                if ($availableSeats <= 0) break;

                $participation = new Participation();
                $participation->setUser($user);
                $participation->setRide($ride);
                $participation->setCreditsUsed($ride->getPrice());

                // Aléatoire entre confirmée ou annulée
                $status = $faker->randomElement(['confirmed', 'canceled', 'pending']);
                $participation->setStatus($status);

                $manager->persist($participation);

                if ($status === 'confirmed') {
                    $availableSeats--;
                }
            }

            $ride->setSeatsAvailable($availableSeats);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            \App\DataFixtures\RideFixtures::class,
            \App\DataFixtures\UserFixtures::class,
        ];
    }
}