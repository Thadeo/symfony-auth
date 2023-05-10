<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509005120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, currency_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, flag LONGTEXT DEFAULT NULL, dial_code VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME DEFAULT NULL, INDEX IDX_5373C96638248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country_state (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, updated_date DATETIME NOT NULL, INDEX IDX_473C711F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, symbol VARCHAR(255) NOT NULL, image LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, updated_date DATETIME DEFAULT NULL, INDEX IDX_6956883FF92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, phone_id INT DEFAULT NULL, address_id INT DEFAULT NULL, account_type_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, account VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649F92F3E70 (country_id), INDEX IDX_8D93D6493B7323CB (phone_id), INDEX IDX_8D93D649F5B7AF75 (address_id), INDEX IDX_8D93D649C6798DB (account_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_account_type (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, code VARCHAR(255) NOT NULL, updated_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_address (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, state_id INT DEFAULT NULL, date DATETIME NOT NULL, city VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, address2 LONGTEXT DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, is_primary TINYINT(1) NOT NULL, is_verified TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_5543718BA76ED395 (user_id), INDEX IDX_5543718B5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_phone (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, country_id INT DEFAULT NULL, date DATETIME NOT NULL, phone VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, is_primary TINYINT(1) NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_A68D6C85A76ED395 (user_id), INDEX IDX_A68D6C85F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C96638248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE country_state ADD CONSTRAINT FK_473C711F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE currency ADD CONSTRAINT FK_6956883FF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493B7323CB FOREIGN KEY (phone_id) REFERENCES user_phone (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F5B7AF75 FOREIGN KEY (address_id) REFERENCES user_address (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C6798DB FOREIGN KEY (account_type_id) REFERENCES user_account_type (id)');
        $this->addSql('ALTER TABLE user_address ADD CONSTRAINT FK_5543718BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_address ADD CONSTRAINT FK_5543718B5D83CC1 FOREIGN KEY (state_id) REFERENCES country_state (id)');
        $this->addSql('ALTER TABLE user_phone ADD CONSTRAINT FK_A68D6C85A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_phone ADD CONSTRAINT FK_A68D6C85F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C96638248176');
        $this->addSql('ALTER TABLE country_state DROP FOREIGN KEY FK_473C711F92F3E70');
        $this->addSql('ALTER TABLE currency DROP FOREIGN KEY FK_6956883FF92F3E70');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493B7323CB');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F5B7AF75');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C6798DB');
        $this->addSql('ALTER TABLE user_address DROP FOREIGN KEY FK_5543718BA76ED395');
        $this->addSql('ALTER TABLE user_address DROP FOREIGN KEY FK_5543718B5D83CC1');
        $this->addSql('ALTER TABLE user_phone DROP FOREIGN KEY FK_A68D6C85A76ED395');
        $this->addSql('ALTER TABLE user_phone DROP FOREIGN KEY FK_A68D6C85F92F3E70');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE country_state');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_account_type');
        $this->addSql('DROP TABLE user_address');
        $this->addSql('DROP TABLE user_phone');
    }
}
