<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613161926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_way (id INT AUTO_INCREMENT NOT NULL, auth_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, verify_type VARCHAR(255) NOT NULL, short_desc LONGTEXT DEFAULT NULL, long_desc LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_692D01C48082819C (auth_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_way ADD CONSTRAINT FK_692D01C48082819C FOREIGN KEY (auth_id) REFERENCES auth (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_way DROP FOREIGN KEY FK_692D01C48082819C');
        $this->addSql('DROP TABLE auth_way');
    }
}
