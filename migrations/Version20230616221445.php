<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616221445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_verify ADD device_id INT DEFAULT NULL, DROP device');
        $this->addSql('ALTER TABLE auth_verify ADD CONSTRAINT FK_81868DA194A4C7D4 FOREIGN KEY (device_id) REFERENCES user_devices (id)');
        $this->addSql('CREATE INDEX IDX_81868DA194A4C7D4 ON auth_verify (device_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_verify DROP FOREIGN KEY FK_81868DA194A4C7D4');
        $this->addSql('DROP INDEX IDX_81868DA194A4C7D4 ON auth_verify');
        $this->addSql('ALTER TABLE auth_verify ADD device LONGTEXT DEFAULT NULL, DROP device_id');
    }
}
