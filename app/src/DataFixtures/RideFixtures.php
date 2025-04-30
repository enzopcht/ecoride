<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RideFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $users = $manager->getRepository(\App\Entity\User::class)->findAll();
        $vehicles = $manager->getRepository(\App\Entity\Vehicle::class)->findAll();

        $towns = ['Paris', 'Lyon', 'Nantes', 'Bordeaux', 'Lille', 'Strasbourg', 'Toulouse', 'Nice', 'Rennes', 'Dijon'];
        $statuts = ['pending', 'active', 'completed'];

        foreach ($statuts as $statut) {
            for ($i = 0; $i < 4; $i++) {
                $driver = $faker->randomElement($users);
                $userVehicles = array_filter($vehicles, fn($v) => $v->getOwner() === $driver);

                if (empty($userVehicles)) {
                    continue;
                }

                $vehicle = $faker->randomElement($userVehicles);

                $departureCity = $faker->randomElement($towns);
                do {
                    $arrivalCity = $faker->randomElement($towns);
                } while ($arrivalCity === $departureCity);

                $departureAddress = $faker->numberBetween(1, 200) . ' ' . $faker->streetName() . ', ' . $departureCity;
                $arrivalAddress = $faker->numberBetween(1, 200) . ' ' . $faker->streetName() . ', ' . $arrivalCity;

                // Départ et durée selon le statut
                if ($statut === 'en_attente') {
                    $departureTime = $faker->dateTimeBetween('+1 hour', '+3 days');
                } elseif ($statut === 'en_cours') {
                    $departureTime = $faker->dateTimeBetween('-1 hour', 'now');
                } else { // terminé
                    $departureTime = $faker->dateTimeBetween('-5 days', '-2 hours');
                }

                $durationMinutes = $faker->numberBetween(60, 180);
                $arrivalTime = (clone $departureTime)->modify("+$durationMinutes minutes");

                $ride = new \App\Entity\Ride();
                $ride->setDriver($driver);
                $ride->setVehicle($vehicle);
                $ride->setDepartureCity($departureCity);
                $ride->setArrivalCity($arrivalCity);
                $ride->setDepartureAddress($departureAddress);
                $ride->setArrivalAddress($arrivalAddress);
                $ride->setDepartureTime(\DateTimeImmutable::createFromMutable($departureTime));
                $ride->setArrivalTime(\DateTimeImmutable::createFromMutable($arrivalTime));
                $ride->setPrice($faker->numberBetween(5, 10));
                $ride->setSeatsAvailable($faker->numberBetween(1, 4));
                $ride->setEcological($vehicle->getCarModel()->getEnergy() === 'électrique');
                $ride->setStatus($statut);

                $manager->persist($ride);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            \App\DataFixtures\UserFixtures::class,
            \App\DataFixtures\VehicleFixtures::class,
        ];
    }
}
