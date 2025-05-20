<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RideFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $userRepo = $manager->getRepository(User::class);

        $users = $manager->getRepository(\App\Entity\User::class)->findAll();
        $drivers = array_filter($userRepo->findAll(), function (User $user) {
            $roles = $user->getRoles();
            return in_array('ROLE_DRIVER', $roles, true) && !in_array('ROLE_ADMIN', $roles, true) && !in_array('ROLE_EMPLOYE', $roles, true);
        });
        $vehicles = $manager->getRepository(\App\Entity\Vehicle::class)->findAll();

        $towns = [
            'Paris' => '75000',
            'Lyon' => '69000',
            'Nantes' => '44000',
            'Bordeaux' => '33000',
            'Lille' => '59000',
            'Strasbourg' => '67000',
            'Toulouse' => '31000',
            'Nice' => '06000',
            'Rennes' => '35000',
            'Dijon' => '21000',
        ];
        $statuts = ['pending', 'active', 'completed'];

        foreach ($statuts as $statut) {
            for ($i = 0; $i < 10; $i++) {
                $driver = $faker->randomElement($drivers);
                $userVehicles = array_filter($vehicles, fn($v) => $v->getOwner() === $driver);

                if (empty($userVehicles)) {
                    continue;
                }

                $vehicle = $faker->randomElement($userVehicles);

                $departureCity = $faker->randomElement(array_keys($towns));
                do {
                    $arrivalCity = $faker->randomElement(array_keys($towns));
                } while ($arrivalCity === $departureCity);

                $departurePostCode = $towns[$departureCity];
                $arrivalPostCode = $towns[$arrivalCity];

                $departureAddress = $faker->numberBetween(1, 200) . ' ' . $faker->streetName() . ', ' . $departureCity;
                $arrivalAddress = $faker->numberBetween(1, 200) . ' ' . $faker->streetName() . ', ' . $arrivalCity;

                // Départ et durée selon le statut
                if ($statut === 'pending') {
                    $departureTime = $faker->dateTimeBetween('+1 hour', '+3 days');
                } elseif ($statut === 'active') {
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
                $ride->setDeparturePostCode($departurePostCode);
                $ride->setArrivalPostCode($arrivalPostCode);
                $ride->setDepartureAddress($departureAddress);
                $ride->setArrivalAddress($arrivalAddress);
                $ride->setDepartureTime(\DateTimeImmutable::createFromMutable($departureTime));
                $ride->setArrivalTime(\DateTimeImmutable::createFromMutable($arrivalTime));
                $ride->setPrice($faker->numberBetween(5, 10));
                $ride->setSeatsAvailable($faker->numberBetween(1, 4));
                $ride->setEcological($vehicle->getCarModel()->getEnergy() === 'electric');
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
