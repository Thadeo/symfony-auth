<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230624002038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roles ADD account_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC7C6798DB FOREIGN KEY (account_type_id) REFERENCES user_account_type (id)');
        $this->addSql('CREATE INDEX IDX_B63E2EC7C6798DB ON roles (account_type_id)');
        $this->addSql('ALTER TABLE user_custom_roles ADD account_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_custom_roles ADD CONSTRAINT FK_F5FFAAACC6798DB FOREIGN KEY (account_type_id) REFERENCES user_account_type (id)');
        $this->addSql('CREATE INDEX IDX_F5FFAAACC6798DB ON user_custom_roles (account_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC7C6798DB');
        $this->addSql('DROP INDEX IDX_B63E2EC7C6798DB ON roles');
        $this->addSql('ALTER TABLE roles DROP account_type_id');
        $this->addSql('ALTER TABLE user_custom_roles DROP FOREIGN KEY FK_F5FFAAACC6798DB');
        $this->addSql('DROP INDEX IDX_F5FFAAACC6798DB ON user_custom_roles');
        $this->addSql('ALTER TABLE user_custom_roles DROP account_type_id');
    }
}
