<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613163019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_type ADD auth_way_id INT DEFAULT NULL, DROP name, DROP code, DROP verify_type, DROP short_desc, DROP long_desc');
        $this->addSql('ALTER TABLE auth_type ADD CONSTRAINT FK_A2F8B79DAC2396F1 FOREIGN KEY (auth_way_id) REFERENCES auth_way (id)');
        $this->addSql('CREATE INDEX IDX_A2F8B79DAC2396F1 ON auth_type (auth_way_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_type DROP FOREIGN KEY FK_A2F8B79DAC2396F1');
        $this->addSql('DROP INDEX IDX_A2F8B79DAC2396F1 ON auth_type');
        $this->addSql('ALTER TABLE auth_type ADD name VARCHAR(255) NOT NULL, ADD code VARCHAR(255) NOT NULL, ADD verify_type VARCHAR(255) NOT NULL, ADD short_desc LONGTEXT DEFAULT NULL, ADD long_desc LONGTEXT DEFAULT NULL, DROP auth_way_id');
    }
}
