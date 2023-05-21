<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230518044955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE settings ADD country_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD CONSTRAINT FK_E545A0C5F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_E545A0C5F92F3E70 ON settings (country_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE settings DROP FOREIGN KEY FK_E545A0C5F92F3E70');
        $this->addSql('DROP INDEX IDX_E545A0C5F92F3E70 ON settings');
        $this->addSql('ALTER TABLE settings DROP country_id');
    }
}
