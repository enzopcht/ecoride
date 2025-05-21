<?php

namespace App\DataFixtures;

use App\Entity\CarBrand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarBrandFixtures extends Fixture
{
    public const BRANDS = [
        'Tesla',
        'Peugeot',
        'Renault',
        'Toyota',
        'Volkswagen',
        'BMW',
        'Mercedes-Benz',
        'Audi',
        'Ford',
        'Citroën',
        'Opel',
        'Hyundai',
        'Kia',
        'Nissan',
        'Mazda',
        'Fiat',
        'Skoda',
        'Honda',
        'Volvo',
        'Dacia'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::BRANDS as $label) {
            $brand = new CarBrand();
            $brand->setLabel($label);
            $manager->persist($brand);
            $this->addReference('brand-' . strtolower($label), $brand);
        }

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['prod_init'];
    }
}
