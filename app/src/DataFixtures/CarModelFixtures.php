<?php

namespace App\DataFixtures;

use App\Entity\CarModel;
use App\Entity\CarBrand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class CarModelFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private ManagerRegistry $doctrine) {}

    public function load(ObjectManager $manager): void
    {
        $brands = [
            'Tesla' => [
                ['label' => 'Model 3', 'energy' => 'electric'],
                ['label' => 'Model S', 'energy' => 'electric'],
                ['label' => 'Model X', 'energy' => 'electric'],
                ['label' => 'Model Y', 'energy' => 'electric']
            ],
            'Peugeot' => [
                ['label' => '208', 'energy' => 'thermal'],
                ['label' => '308', 'energy' => 'thermal'],
                ['label' => 'e-208', 'energy' => 'electric'],
                ['label' => 'e-308', 'energy' => 'electric']
            ],
            'Renault' => [
                ['label' => 'Clio', 'energy' => 'thermal'],
                ['label' => 'Mégane', 'energy' => 'thermal'],
                ['label' => 'Zoé', 'energy' => 'electric'],
                ['label' => 'Scenic E-Tech', 'energy' => 'electric']
            ],
            'Toyota' => [
                ['label' => 'Corolla', 'energy' => 'thermal'],
                ['label' => 'Yaris', 'energy' => 'thermal'],
                ['label' => 'Prius', 'energy' => 'electric'],
                ['label' => 'bZ4X', 'energy' => 'electric']
            ],
            'Volkswagen' => [
                ['label' => 'Golf', 'energy' => 'thermal'],
                ['label' => 'Passat', 'energy' => 'thermal'],
                ['label' => 'ID.3', 'energy' => 'electric'],
                ['label' => 'ID.4', 'energy' => 'electric']
            ],
            'BMW' => [
                ['label' => 'Série 1', 'energy' => 'thermal'],
                ['label' => 'Série 3', 'energy' => 'thermal'],
                ['label' => 'i3', 'energy' => 'electric'],
                ['label' => 'i4', 'energy' => 'electric']
            ],
            'Mercedes-Benz' => [
                ['label' => 'Classe A', 'energy' => 'thermal'],
                ['label' => 'Classe C', 'energy' => 'thermal'],
                ['label' => 'EQA', 'energy' => 'electric'],
                ['label' => 'EQC', 'energy' => 'electric']
            ],
            'Audi' => [
                ['label' => 'A3', 'energy' => 'thermal'],
                ['label' => 'A4', 'energy' => 'thermal'],
                ['label' => 'e-tron', 'energy' => 'electric'],
                ['label' => 'Q4 e-tron', 'energy' => 'electric']
            ],
            'Ford' => [
                ['label' => 'Focus', 'energy' => 'thermal'],
                ['label' => 'Fiesta', 'energy' => 'thermal'],
                ['label' => 'Mustang Mach-E', 'energy' => 'electric'],
                ['label' => 'Kuga PHEV', 'energy' => 'electric']
            ],
            'Citroën' => [
                ['label' => 'C3', 'energy' => 'thermal'],
                ['label' => 'C5 Aircross', 'energy' => 'thermal'],
                ['label' => 'ë-C4', 'energy' => 'electric'],
                ['label' => 'AMI', 'energy' => 'electric']
            ],
            'Opel' => [
                ['label' => 'Corsa', 'energy' => 'thermal'],
                ['label' => 'Astra', 'energy' => 'thermal'],
                ['label' => 'Corsa-e', 'energy' => 'electric'],
                ['label' => 'Mokka-e', 'energy' => 'electric']
            ],
            'Hyundai' => [
                ['label' => 'i20', 'energy' => 'thermal'],
                ['label' => 'i30', 'energy' => 'thermal'],
                ['label' => 'Kona EV', 'energy' => 'electric'],
                ['label' => 'IONIQ 5', 'energy' => 'electric']
            ],
            'Kia' => [
                ['label' => 'Rio', 'energy' => 'thermal'],
                ['label' => 'Ceed', 'energy' => 'thermal'],
                ['label' => 'e-Niro', 'energy' => 'electric'],
                ['label' => 'EV6', 'energy' => 'electric']
            ],
            'Nissan' => [
                ['label' => 'Micra', 'energy' => 'thermal'],
                ['label' => 'Juke', 'energy' => 'thermal'],
                ['label' => 'Leaf', 'energy' => 'electric'],
                ['label' => 'Ariya', 'energy' => 'electric']
            ],
            'Mazda' => [
                ['label' => 'Mazda2', 'energy' => 'thermal'],
                ['label' => 'Mazda3', 'energy' => 'thermal'],
                ['label' => 'MX-30', 'energy' => 'electric'],
                ['label' => 'CX-30 EV', 'energy' => 'electric']
            ],
            'Fiat' => [
                ['label' => 'Panda', 'energy' => 'thermal'],
                ['label' => 'Tipo', 'energy' => 'thermal'],
                ['label' => '500e', 'energy' => 'electric'],
                ['label' => '600e', 'energy' => 'electric']
            ],
            'Skoda' => [
                ['label' => 'Fabia', 'energy' => 'thermal'],
                ['label' => 'Octavia', 'energy' => 'thermal'],
                ['label' => 'Enyaq', 'energy' => 'electric'],
                ['label' => 'Citigo-e', 'energy' => 'electric']
            ],
            'Honda' => [
                ['label' => 'Civic', 'energy' => 'thermal'],
                ['label' => 'Jazz', 'energy' => 'thermal'],
                ['label' => 'Honda e', 'energy' => 'electric'],
                ['label' => 'CR-V Hybrid', 'energy' => 'electric']
            ],
            'Volvo' => [
                ['label' => 'V40', 'energy' => 'thermal'],
                ['label' => 'XC60', 'energy' => 'thermal'],
                ['label' => 'XC40 Recharge', 'energy' => 'electric'],
                ['label' => 'C40 Recharge', 'energy' => 'electric']
            ],
            'Dacia' => [
                ['label' => 'Sandero', 'energy' => 'thermal'],
                ['label' => 'Duster', 'energy' => 'thermal'],
                ['label' => 'Spring', 'energy' => 'electric']
            ]
        ];

        foreach ($brands as $brandLabel => $models) {
            $brand = $manager->getRepository(CarBrand::class)->findOneBy(['label' => $brandLabel]);
            if (!$brand) {
                throw new \RuntimeException("Marque {$brandLabel} non trouvée.");
            }

            foreach ($models as $modelData) {
                $model = new CarModel();
                $model->setLabel($modelData['label']);
                $model->setEnergy($modelData['energy']);
                $model->setBrand($brand);
                $manager->persist($model);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CarBrandFixtures::class,
        ];
    }
}