<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230520092652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sessions ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9A609D1370DAA798 ON sessions (ids)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sessions MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_9A609D1370DAA798 ON sessions');
        $this->addSql('DROP INDEX `PRIMARY` ON sessions');
        $this->addSql('ALTER TABLE sessions DROP id');
        $this->addSql('ALTER TABLE sessions ADD PRIMARY KEY (ids)');
    }
}
