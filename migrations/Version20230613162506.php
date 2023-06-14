<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613162506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_way DROP FOREIGN KEY FK_692D01C48082819C');
        $this->addSql('DROP INDEX IDX_692D01C48082819C ON auth_way');
        $this->addSql('ALTER TABLE auth_way DROP auth_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_way ADD auth_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE auth_way ADD CONSTRAINT FK_692D01C48082819C FOREIGN KEY (auth_id) REFERENCES auth (id)');
        $this->addSql('CREATE INDEX IDX_692D01C48082819C ON auth_way (auth_id)');
    }
}
