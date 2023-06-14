<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230614013647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_type_provider DROP FOREIGN KEY FK_58A88870C54C8C93');
        $this->addSql('ALTER TABLE auth_type_provider_settings DROP FOREIGN KEY FK_2F61AF8CA53A8AA');
        $this->addSql('DROP TABLE auth_type_provider');
        $this->addSql('DROP TABLE auth_type_provider_settings');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_type_provider (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, orderling VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, is_primary TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_58A88870C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE auth_type_provider_settings (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, value LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, short_desc LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, long_desc LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, updated_date DATETIME NOT NULL, INDEX IDX_2F61AF8CA53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE auth_type_provider ADD CONSTRAINT FK_58A88870C54C8C93 FOREIGN KEY (type_id) REFERENCES auth_type (id)');
        $this->addSql('ALTER TABLE auth_type_provider_settings ADD CONSTRAINT FK_2F61AF8CA53A8AA FOREIGN KEY (provider_id) REFERENCES auth_type_provider (id)');
    }
}
