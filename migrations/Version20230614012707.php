<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230614012707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_way_provider_settings (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, short_desc LONGTEXT DEFAULT NULL, long_desc LONGTEXT DEFAULT NULL, updated_date DATETIME NOT NULL, identifier VARCHAR(255) NOT NULL, INDEX IDX_D37399C0A53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_way_provider_settings ADD CONSTRAINT FK_D37399C0A53A8AA FOREIGN KEY (provider_id) REFERENCES auth_way_provider (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_way_provider_settings DROP FOREIGN KEY FK_D37399C0A53A8AA');
        $this->addSql('DROP TABLE auth_way_provider_settings');
    }
}
