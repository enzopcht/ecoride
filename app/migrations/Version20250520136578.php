<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520136578 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
        INSERT INTO car_brand (label) VALUES
        ('Tesla'), ('Peugeot'), ('Renault'), ('Toyota'), ('Volkswagen'),
        ('BMW'), ('Mercedes-Benz'), ('Audi'), ('Ford'), ('Citroën'),
        ('Opel'), ('Hyundai'), ('Kia'), ('Nissan'), ('Mazda'),
        ('Fiat'), ('Skoda'), ('Honda'), ('Volvo'), ('Dacia')
    ");

        $brandsModels = [
            'Tesla' => [
                ['label' => 'Model 3', 'energy' => 'electric'],
                ['label' => 'Model S', 'energy' => 'electric'],
                ['label' => 'Model X', 'energy' => 'electric'],
                ['label' => 'Model Y', 'energy' => 'electric'],
            ],
            'Peugeot' => [
                ['label' => '208', 'energy' => 'thermal'],
                ['label' => '308', 'energy' => 'thermal'],
                ['label' => 'e-208', 'energy' => 'electric'],
                ['label' => 'e-308', 'energy' => 'electric'],
            ],
            'Renault' => [
                ['label' => 'Clio', 'energy' => 'thermal'],
                ['label' => 'Mégane', 'energy' => 'thermal'],
                ['label' => 'Zoé', 'energy' => 'electric'],
                ['label' => 'Scenic E-Tech', 'energy' => 'electric'],
            ],
            'Toyota' => [
                ['label' => 'Corolla', 'energy' => 'thermal'],
                ['label' => 'Yaris', 'energy' => 'thermal'],
                ['label' => 'Prius', 'energy' => 'electric'],
                ['label' => 'bZ4X', 'energy' => 'electric'],
            ],
            'Volkswagen' => [
                ['label' => 'Golf', 'energy' => 'thermal'],
                ['label' => 'Passat', 'energy' => 'thermal'],
                ['label' => 'ID.3', 'energy' => 'electric'],
                ['label' => 'ID.4', 'energy' => 'electric'],
            ],
            'BMW' => [
                ['label' => 'Série 1', 'energy' => 'thermal'],
                ['label' => 'Série 3', 'energy' => 'thermal'],
                ['label' => 'i3', 'energy' => 'electric'],
                ['label' => 'i4', 'energy' => 'electric'],
            ],
            'Mercedes-Benz' => [
                ['label' => 'Classe A', 'energy' => 'thermal'],
                ['label' => 'Classe C', 'energy' => 'thermal'],
                ['label' => 'EQA', 'energy' => 'electric'],
                ['label' => 'EQC', 'energy' => 'electric'],
            ],
            'Audi' => [
                ['label' => 'A3', 'energy' => 'thermal'],
                ['label' => 'A4', 'energy' => 'thermal'],
                ['label' => 'e-tron', 'energy' => 'electric'],
                ['label' => 'Q4 e-tron', 'energy' => 'electric'],
            ],
            'Ford' => [
                ['label' => 'Focus', 'energy' => 'thermal'],
                ['label' => 'Fiesta', 'energy' => 'thermal'],
                ['label' => 'Mustang Mach-E', 'energy' => 'electric'],
                ['label' => 'Kuga PHEV', 'energy' => 'electric'],
            ],
            'Citroën' => [
                ['label' => 'C3', 'energy' => 'thermal'],
                ['label' => 'C5 Aircross', 'energy' => 'thermal'],
                ['label' => 'ë-C4', 'energy' => 'electric'],
                ['label' => 'AMI', 'energy' => 'electric'],
            ],
            'Opel' => [
                ['label' => 'Corsa', 'energy' => 'thermal'],
                ['label' => 'Astra', 'energy' => 'thermal'],
                ['label' => 'Corsa-e', 'energy' => 'electric'],
                ['label' => 'Mokka-e', 'energy' => 'electric'],
            ],
            'Hyundai' => [
                ['label' => 'i20', 'energy' => 'thermal'],
                ['label' => 'i30', 'energy' => 'thermal'],
                ['label' => 'Kona EV', 'energy' => 'electric'],
                ['label' => 'IONIQ 5', 'energy' => 'electric'],
            ],
            'Kia' => [
                ['label' => 'Rio', 'energy' => 'thermal'],
                ['label' => 'Ceed', 'energy' => 'thermal'],
                ['label' => 'e-Niro', 'energy' => 'electric'],
                ['label' => 'EV6', 'energy' => 'electric'],
            ],
            'Nissan' => [
                ['label' => 'Micra', 'energy' => 'thermal'],
                ['label' => 'Juke', 'energy' => 'thermal'],
                ['label' => 'Leaf', 'energy' => 'electric'],
                ['label' => 'Ariya', 'energy' => 'electric'],
            ],
            'Mazda' => [
                ['label' => 'Mazda2', 'energy' => 'thermal'],
                ['label' => 'Mazda3', 'energy' => 'thermal'],
                ['label' => 'MX-30', 'energy' => 'electric'],
                ['label' => 'CX-30 EV', 'energy' => 'electric'],
            ],
            'Fiat' => [
                ['label' => 'Panda', 'energy' => 'thermal'],
                ['label' => 'Tipo', 'energy' => 'thermal'],
                ['label' => '500e', 'energy' => 'electric'],
                ['label' => '600e', 'energy' => 'electric'],
            ],
            'Skoda' => [
                ['label' => 'Fabia', 'energy' => 'thermal'],
                ['label' => 'Octavia', 'energy' => 'thermal'],
                ['label' => 'Enyaq', 'energy' => 'electric'],
                ['label' => 'Citigo-e', 'energy' => 'electric'],
            ],
            'Honda' => [
                ['label' => 'Civic', 'energy' => 'thermal'],
                ['label' => 'Jazz', 'energy' => 'thermal'],
                ['label' => 'Honda e', 'energy' => 'electric'],
                ['label' => 'CR-V Hybrid', 'energy' => 'electric'],
            ],
            'Volvo' => [
                ['label' => 'V40', 'energy' => 'thermal'],
                ['label' => 'XC60', 'energy' => 'thermal'],
                ['label' => 'XC40 Recharge', 'energy' => 'electric'],
                ['label' => 'C40 Recharge', 'energy' => 'electric'],
            ],
            'Dacia' => [
                ['label' => 'Sandero', 'energy' => 'thermal'],
                ['label' => 'Duster', 'energy' => 'thermal'],
                ['label' => 'Spring', 'energy' => 'electric'],
            ],
        ];

        foreach ($brandsModels as $brand => $models) {
            foreach ($models as $model) {
                $label = addslashes($model['label']);
                $energy = $model['energy'];
                $this->addSql("
                INSERT INTO car_model (label, energy, brand_id)
                VALUES ('{$label}', '{$energy}', (SELECT id FROM car_brand WHERE label = '{$brand}'))
            ");
            }
        }
    }

    public function down(Schema $schema): void
    {
        // 1. Supprimer d'abord tous les modèles associés aux marques (clé étrangère)
        $this->addSql("
        DELETE FROM car_model
        WHERE brand_id IN (
            SELECT id FROM car_brand WHERE label IN (
                'Tesla', 'Peugeot', 'Renault', 'Toyota', 'Volkswagen',
                'BMW', 'Mercedes-Benz', 'Audi', 'Ford', 'Citroën',
                'Opel', 'Hyundai', 'Kia', 'Nissan', 'Mazda',
                'Fiat', 'Skoda', 'Honda', 'Volvo', 'Dacia'
            )
        )
    ");

        // 2. Supprimer les marques ajoutées
        $this->addSql("
        DELETE FROM car_brand WHERE label IN (
            'Tesla', 'Peugeot', 'Renault', 'Toyota', 'Volkswagen',
            'BMW', 'Mercedes-Benz', 'Audi', 'Ford', 'Citroën',
            'Opel', 'Hyundai', 'Kia', 'Nissan', 'Mazda',
            'Fiat', 'Skoda', 'Honda', 'Volvo', 'Dacia'
        )
    ");
    }
}
