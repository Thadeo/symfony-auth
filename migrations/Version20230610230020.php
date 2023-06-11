<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230610230020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_settings (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, short_desc LONGTEXT DEFAULT NULL, long_desc LONGTEXT DEFAULT NULL, updated_date DATETIME NOT NULL, INDEX IDX_5C844C5A76ED395 (user_id), INDEX IDX_5C844C5C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_settings_type (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT FK_5C844C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_settings ADD CONSTRAINT FK_5C844C5C54C8C93 FOREIGN KEY (type_id) REFERENCES user_settings_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_settings DROP FOREIGN KEY FK_5C844C5A76ED395');
        $this->addSql('ALTER TABLE user_settings DROP FOREIGN KEY FK_5C844C5C54C8C93');
        $this->addSql('DROP TABLE user_settings');
        $this->addSql('DROP TABLE user_settings_type');
    }
}
