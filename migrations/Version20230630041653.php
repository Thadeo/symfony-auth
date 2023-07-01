<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230630041653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country_state ADD country_code VARCHAR(255) DEFAULT NULL, ADD fips_code VARCHAR(255) DEFAULT NULL, ADD type VARCHAR(255) DEFAULT NULL, ADD latitude NUMERIC(20, 8) DEFAULT NULL, ADD longitude NUMERIC(20, 8) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country_state DROP country_code, DROP fips_code, DROP type, DROP latitude, DROP longitude');
    }
}
