<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230521015642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_activity (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, date DATETIME NOT NULL, browser LONGTEXT DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, location LONGTEXT DEFAULT NULL, location_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', mode VARCHAR(255) NOT NULL, updated_date DATETIME DEFAULT NULL, INDEX IDX_4CF9ED5AA76ED395 (user_id), INDEX IDX_4CF9ED5A12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_activity_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, long_desc LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_activity ADD CONSTRAINT FK_4CF9ED5AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_activity ADD CONSTRAINT FK_4CF9ED5A12469DE2 FOREIGN KEY (category_id) REFERENCES user_activity_category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_activity DROP FOREIGN KEY FK_4CF9ED5AA76ED395');
        $this->addSql('ALTER TABLE user_activity DROP FOREIGN KEY FK_4CF9ED5A12469DE2');
        $this->addSql('DROP TABLE user_activity');
        $this->addSql('DROP TABLE user_activity_category');
    }
}
