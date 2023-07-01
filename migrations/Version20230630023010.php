<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230630023010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE country_region (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country_sub_region (id INT AUTO_INCREMENT NOT NULL, region_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_F0343C6798260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE country_sub_region ADD CONSTRAINT FK_F0343C6798260155 FOREIGN KEY (region_id) REFERENCES country_region (id)');
        $this->addSql('ALTER TABLE country ADD region_id INT DEFAULT NULL, ADD sub_region_id INT DEFAULT NULL, ADD iso VARCHAR(255) DEFAULT NULL, ADD iso_numeric VARCHAR(255) DEFAULT NULL, ADD iso_number VARCHAR(255) DEFAULT NULL, ADD capital VARCHAR(255) DEFAULT NULL, ADD currency_name VARCHAR(255) DEFAULT NULL, ADD tid VARCHAR(255) DEFAULT NULL, ADD timezones LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD latitude NUMERIC(10, 8) DEFAULT NULL, ADD longitude NUMERIC(20, 8) DEFAULT NULL');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C96698260155 FOREIGN KEY (region_id) REFERENCES country_region (id)');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C9668A2B47EB FOREIGN KEY (sub_region_id) REFERENCES country_sub_region (id)');
        $this->addSql('CREATE INDEX IDX_5373C96698260155 ON country (region_id)');
        $this->addSql('CREATE INDEX IDX_5373C9668A2B47EB ON country (sub_region_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C96698260155');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C9668A2B47EB');
        $this->addSql('ALTER TABLE country_sub_region DROP FOREIGN KEY FK_F0343C6798260155');
        $this->addSql('DROP TABLE country_region');
        $this->addSql('DROP TABLE country_sub_region');
        $this->addSql('DROP INDEX IDX_5373C96698260155 ON country');
        $this->addSql('DROP INDEX IDX_5373C9668A2B47EB ON country');
        $this->addSql('ALTER TABLE country DROP region_id, DROP sub_region_id, DROP iso, DROP iso_numeric, DROP iso_number, DROP capital, DROP currency_name, DROP tid, DROP timezones, DROP latitude, DROP longitude');
    }
}
