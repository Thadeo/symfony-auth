<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616235034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_activity ADD device_id INT DEFAULT NULL, ADD long_desc LONGTEXT DEFAULT NULL, DROP browser, DROP ip, DROP location, DROP location_data, DROP session');
        $this->addSql('ALTER TABLE user_activity ADD CONSTRAINT FK_4CF9ED5A94A4C7D4 FOREIGN KEY (device_id) REFERENCES user_devices (id)');
        $this->addSql('CREATE INDEX IDX_4CF9ED5A94A4C7D4 ON user_activity (device_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_activity DROP FOREIGN KEY FK_4CF9ED5A94A4C7D4');
        $this->addSql('DROP INDEX IDX_4CF9ED5A94A4C7D4 ON user_activity');
        $this->addSql('ALTER TABLE user_activity ADD ip VARCHAR(255) DEFAULT NULL, ADD location LONGTEXT DEFAULT NULL, ADD location_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD session VARCHAR(255) DEFAULT NULL, DROP device_id, CHANGE long_desc browser LONGTEXT DEFAULT NULL');
    }
}
