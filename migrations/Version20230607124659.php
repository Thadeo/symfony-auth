<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230607124659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_type (id INT AUTO_INCREMENT NOT NULL, auth_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_A2F8B79D8082819C (auth_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_type_provider (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, orderling VARCHAR(255) DEFAULT NULL, is_primary TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_58A88870C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_type_provider_settings (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, short_desc LONGTEXT DEFAULT NULL, long_desc LONGTEXT DEFAULT NULL, updated_date DATETIME NOT NULL, INDEX IDX_2F61AF8CA53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auth_type ADD CONSTRAINT FK_A2F8B79D8082819C FOREIGN KEY (auth_id) REFERENCES auth (id)');
        $this->addSql('ALTER TABLE auth_type_provider ADD CONSTRAINT FK_58A88870C54C8C93 FOREIGN KEY (type_id) REFERENCES auth_type (id)');
        $this->addSql('ALTER TABLE auth_type_provider_settings ADD CONSTRAINT FK_2F61AF8CA53A8AA FOREIGN KEY (provider_id) REFERENCES auth_type_provider (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_type DROP FOREIGN KEY FK_A2F8B79D8082819C');
        $this->addSql('ALTER TABLE auth_type_provider DROP FOREIGN KEY FK_58A88870C54C8C93');
        $this->addSql('ALTER TABLE auth_type_provider_settings DROP FOREIGN KEY FK_2F61AF8CA53A8AA');
        $this->addSql('DROP TABLE auth');
        $this->addSql('DROP TABLE auth_type');
        $this->addSql('DROP TABLE auth_type_provider');
        $this->addSql('DROP TABLE auth_type_provider_settings');
    }
}
