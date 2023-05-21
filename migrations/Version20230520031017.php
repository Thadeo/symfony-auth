<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230520031017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sessions (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ids VARBINARY(255) NOT NULL, date DATETIME NOT NULL, ip VARCHAR(255) DEFAULT NULL, browser LONGTEXT DEFAULT NULL, last_login DATETIME DEFAULT NULL, data LONGBLOB DEFAULT NULL, time INT NOT NULL, lifetime INT NOT NULL, UNIQUE INDEX UNIQ_9A609D1370DAA798 (ids), INDEX IDX_9A609D13A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sessions ADD CONSTRAINT FK_9A609D13A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sessions DROP FOREIGN KEY FK_9A609D13A76ED395');
        $this->addSql('DROP TABLE sessions');
    }
}
