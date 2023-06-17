<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230617004451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sessions ADD device_id INT DEFAULT NULL, DROP ip, DROP browser, DROP location, DROP location_data, DROP last_login');
        $this->addSql('ALTER TABLE sessions ADD CONSTRAINT FK_9A609D1394A4C7D4 FOREIGN KEY (device_id) REFERENCES user_devices (id)');
        $this->addSql('CREATE INDEX IDX_9A609D1394A4C7D4 ON sessions (device_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sessions DROP FOREIGN KEY FK_9A609D1394A4C7D4');
        $this->addSql('DROP INDEX IDX_9A609D1394A4C7D4 ON sessions');
        $this->addSql('ALTER TABLE sessions ADD ip VARCHAR(255) DEFAULT NULL, ADD browser LONGTEXT DEFAULT NULL, ADD location LONGTEXT DEFAULT NULL, ADD location_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD last_login DATETIME DEFAULT NULL, DROP device_id');
    }
}
