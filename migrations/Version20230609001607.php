<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230609001607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_verify (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, auth_type_id INT DEFAULT NULL, date DATETIME NOT NULL, token VARCHAR(255) DEFAULT NULL, device LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, INDEX IDX_81868DA1A76ED395 (user_id), INDEX IDX_81868DA18B68AC60 (auth_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_verify ADD CONSTRAINT FK_81868DA1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE auth_verify ADD CONSTRAINT FK_81868DA18B68AC60 FOREIGN KEY (auth_type_id) REFERENCES auth_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_verify DROP FOREIGN KEY FK_81868DA1A76ED395');
        $this->addSql('ALTER TABLE auth_verify DROP FOREIGN KEY FK_81868DA18B68AC60');
        $this->addSql('DROP TABLE auth_verify');
    }
}
