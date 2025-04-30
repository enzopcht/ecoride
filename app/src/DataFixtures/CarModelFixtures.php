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
                ['label' => 'Model 3', 'energy' => 'électrique'],
                ['label' => 'Model S', 'energy' => 'électrique'],
                ['label' => 'Model X', 'energy' => 'électrique'],
                ['label' => 'Model Y', 'energy' => 'électrique']
            ],
            'Peugeot' => [
                ['label' => '208', 'energy' => 'thermique'],
                ['label' => '308', 'energy' => 'thermique'],
                ['label' => 'e-208', 'energy' => 'électrique'],
                ['label' => 'e-308', 'energy' => 'électrique']
            ],
            'Renault' => [
                ['label' => 'Clio', 'energy' => 'thermique'],
                ['label' => 'Mégane', 'energy' => 'thermique'],
                ['label' => 'Zoé', 'energy' => 'électrique'],
                ['label' => 'Scenic E-Tech', 'energy' => 'électrique']
            ],
            'Toyota' => [
                ['label' => 'Corolla', 'energy' => 'thermique'],
                ['label' => 'Yaris', 'energy' => 'thermique'],
                ['label' => 'Prius', 'energy' => 'électrique'],
                ['label' => 'bZ4X', 'energy' => 'électrique']
            ],
            'Volkswagen' => [
                ['label' => 'Golf', 'energy' => 'thermique'],
                ['label' => 'Passat', 'energy' => 'thermique'],
                ['label' => 'ID.3', 'energy' => 'électrique'],
                ['label' => 'ID.4', 'energy' => 'électrique']
            ],
            'BMW' => [
                ['label' => 'Série 1', 'energy' => 'thermique'],
                ['label' => 'Série 3', 'energy' => 'thermique'],
                ['label' => 'i3', 'energy' => 'électrique'],
                ['label' => 'i4', 'energy' => 'électrique']
            ],
            'Mercedes-Benz' => [
                ['label' => 'Classe A', 'energy' => 'thermique'],
                ['label' => 'Classe C', 'energy' => 'thermique'],
                ['label' => 'EQA', 'energy' => 'électrique'],
                ['label' => 'EQC', 'energy' => 'électrique']
            ],
            'Audi' => [
                ['label' => 'A3', 'energy' => 'thermique'],
                ['label' => 'A4', 'energy' => 'thermique'],
                ['label' => 'e-tron', 'energy' => 'électrique'],
                ['label' => 'Q4 e-tron', 'energy' => 'électrique']
            ],
            'Ford' => [
                ['label' => 'Focus', 'energy' => 'thermique'],
                ['label' => 'Fiesta', 'energy' => 'thermique'],
                ['label' => 'Mustang Mach-E', 'energy' => 'électrique'],
                ['label' => 'Kuga PHEV', 'energy' => 'électrique']
            ],
            'Citroën' => [
                ['label' => 'C3', 'energy' => 'thermique'],
                ['label' => 'C5 Aircross', 'energy' => 'thermique'],
                ['label' => 'ë-C4', 'energy' => 'électrique'],
                ['label' => 'AMI', 'energy' => 'électrique']
            ],
            'Opel' => [
                ['label' => 'Corsa', 'energy' => 'thermique'],
                ['label' => 'Astra', 'energy' => 'thermique'],
                ['label' => 'Corsa-e', 'energy' => 'électrique'],
                ['label' => 'Mokka-e', 'energy' => 'électrique']
            ],
            'Hyundai' => [
                ['label' => 'i20', 'energy' => 'thermique'],
                ['label' => 'i30', 'energy' => 'thermique'],
                ['label' => 'Kona EV', 'energy' => 'électrique'],
                ['label' => 'IONIQ 5', 'energy' => 'électrique']
            ],
            'Kia' => [
                ['label' => 'Rio', 'energy' => 'thermique'],
                ['label' => 'Ceed', 'energy' => 'thermique'],
                ['label' => 'e-Niro', 'energy' => 'électrique'],
                ['label' => 'EV6', 'energy' => 'électrique']
            ],
            'Nissan' => [
                ['label' => 'Micra', 'energy' => 'thermique'],
                ['label' => 'Juke', 'energy' => 'thermique'],
                ['label' => 'Leaf', 'energy' => 'électrique'],
                ['label' => 'Ariya', 'energy' => 'électrique']
            ],
            'Mazda' => [
                ['label' => 'Mazda2', 'energy' => 'thermique'],
                ['label' => 'Mazda3', 'energy' => 'thermique'],
                ['label' => 'MX-30', 'energy' => 'électrique'],
                ['label' => 'CX-30 EV', 'energy' => 'électrique']
            ],
            'Fiat' => [
                ['label' => 'Panda', 'energy' => 'thermique'],
                ['label' => 'Tipo', 'energy' => 'thermique'],
                ['label' => '500e', 'energy' => 'électrique'],
                ['label' => '600e', 'energy' => 'électrique']
            ],
            'Skoda' => [
                ['label' => 'Fabia', 'energy' => 'thermique'],
                ['label' => 'Octavia', 'energy' => 'thermique'],
                ['label' => 'Enyaq', 'energy' => 'électrique'],
                ['label' => 'Citigo-e', 'energy' => 'électrique']
            ],
            'Honda' => [
                ['label' => 'Civic', 'energy' => 'thermique'],
                ['label' => 'Jazz', 'energy' => 'thermique'],
                ['label' => 'Honda e', 'energy' => 'électrique'],
                ['label' => 'CR-V Hybrid', 'energy' => 'électrique']
            ],
            'Volvo' => [
                ['label' => 'V40', 'energy' => 'thermique'],
                ['label' => 'XC60', 'energy' => 'thermique'],
                ['label' => 'XC40 Recharge', 'energy' => 'électrique'],
                ['label' => 'C40 Recharge', 'energy' => 'électrique']
            ],
            'Dacia' => [
                ['label' => 'Sandero', 'energy' => 'thermique'],
                ['label' => 'Duster', 'energy' => 'thermique'],
                ['label' => 'Spring', 'energy' => 'électrique']
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