<?php

namespace App\DataFixtures;

use App\Entity\Vehicle;
use App\Entity\CarModel;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class VehicleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $carModelRepo = $manager->getRepository(CarModel::class);
        $userRepo = $manager->getRepository(User::class);

        $users = $userRepo->findAll();
        $models = $carModelRepo->findAll();

        if (empty($models)) {
            throw new \RuntimeException('Aucun modèle disponible pour créer les véhicules.');
        }

        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                $vehicle = new Vehicle();
                $vehicle->setPlate(strtoupper($faker->bothify('??-###-??')));
                $vehicle->setColor($faker->safeColorName());
                $vehicle->setFirstRegistrationDate($faker->dateTimeBetween('-10 years', 'now'));
                $vehicle->setCarModel($faker->randomElement($models));
                $vehicle->setOwner($user);

                $manager->persist($vehicle);
            }
        }

        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            \App\DataFixtures\CarModelFixtures::class,
        ];
    }
}

