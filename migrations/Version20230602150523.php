<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602150523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, updated_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE translation (id INT AUTO_INCREMENT NOT NULL, language_id INT DEFAULT NULL, date DATETIME NOT NULL, locale VARCHAR(255) NOT NULL, code LONGTEXT NOT NULL, message LONGTEXT DEFAULT NULL, domain VARCHAR(255) DEFAULT NULL, updated_date DATETIME NOT NULL, INDEX IDX_B469456F82F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE translation ADD CONSTRAINT FK_B469456F82F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE translation DROP FOREIGN KEY FK_B469456F82F1BAF4');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE translation');
    }
}
