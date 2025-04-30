<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430100904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ride ADD departure_city VARCHAR(255) NOT NULL, ADD departure_address VARCHAR(255) NOT NULL, ADD arrival_city VARCHAR(255) NOT NULL, ADD arrival_address VARCHAR(255) NOT NULL, DROP departure, DROP arrival
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ride ADD departure VARCHAR(255) NOT NULL, ADD arrival VARCHAR(255) NOT NULL, DROP departure_city, DROP departure_address, DROP arrival_city, DROP arrival_address
        SQL);
    }
}
