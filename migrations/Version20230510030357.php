<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230510030357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, notes VARCHAR(255) NOT NULL, long_desc LONGTEXT DEFAULT NULL, updated_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles_category (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, notes VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles_permission (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, role_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, notes VARCHAR(255) DEFAULT NULL, long_desc LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_AC6EAC2212469DE2 (category_id), INDEX IDX_AC6EAC22D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_custom_roles (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, notes VARCHAR(255) DEFAULT NULL, long_desc LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_F5FFAAACA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_custom_roles_permission (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, permission_id INT DEFAULT NULL, user_id INT DEFAULT NULL, date DATETIME NOT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_38DBC620D60322AC (role_id), INDEX IDX_38DBC620FED90CCA (permission_id), INDEX IDX_38DBC620A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, role_id INT DEFAULT NULL, custom_id INT DEFAULT NULL, date DATETIME NOT NULL, type VARCHAR(255) NOT NULL, updated_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_54FCD59FA76ED395 (user_id), INDEX IDX_54FCD59FD60322AC (role_id), INDEX IDX_54FCD59F614A603A (custom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE roles_permission ADD CONSTRAINT FK_AC6EAC2212469DE2 FOREIGN KEY (category_id) REFERENCES roles_category (id)');
        $this->addSql('ALTER TABLE roles_permission ADD CONSTRAINT FK_AC6EAC22D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE user_custom_roles ADD CONSTRAINT FK_F5FFAAACA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_custom_roles_permission ADD CONSTRAINT FK_38DBC620D60322AC FOREIGN KEY (role_id) REFERENCES user_custom_roles (id)');
        $this->addSql('ALTER TABLE user_custom_roles_permission ADD CONSTRAINT FK_38DBC620FED90CCA FOREIGN KEY (permission_id) REFERENCES roles_permission (id)');
        $this->addSql('ALTER TABLE user_custom_roles_permission ADD CONSTRAINT FK_38DBC620A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59F614A603A FOREIGN KEY (custom_id) REFERENCES user_custom_roles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roles_permission DROP FOREIGN KEY FK_AC6EAC2212469DE2');
        $this->addSql('ALTER TABLE roles_permission DROP FOREIGN KEY FK_AC6EAC22D60322AC');
        $this->addSql('ALTER TABLE user_custom_roles DROP FOREIGN KEY FK_F5FFAAACA76ED395');
        $this->addSql('ALTER TABLE user_custom_roles_permission DROP FOREIGN KEY FK_38DBC620D60322AC');
        $this->addSql('ALTER TABLE user_custom_roles_permission DROP FOREIGN KEY FK_38DBC620FED90CCA');
        $this->addSql('ALTER TABLE user_custom_roles_permission DROP FOREIGN KEY FK_38DBC620A76ED395');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FA76ED395');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FD60322AC');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59F614A603A');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE roles_category');
        $this->addSql('DROP TABLE roles_permission');
        $this->addSql('DROP TABLE user_custom_roles');
        $this->addSql('DROP TABLE user_custom_roles_permission');
        $this->addSql('DROP TABLE user_roles');
    }
}
