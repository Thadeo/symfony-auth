<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613172457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_way_provider (id INT AUTO_INCREMENT NOT NULL, way_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, orderling VARCHAR(255) DEFAULT NULL, is_primary TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_141462E88C803113 (way_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_way_provider ADD CONSTRAINT FK_141462E88C803113 FOREIGN KEY (way_id) REFERENCES auth_way (id)');
        $this->addSql('ALTER TABLE auth_way DROP code');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_way_provider DROP FOREIGN KEY FK_141462E88C803113');
        $this->addSql('DROP TABLE auth_way_provider');
        $this->addSql('ALTER TABLE auth_way ADD code VARCHAR(255) NOT NULL');
    }
}
