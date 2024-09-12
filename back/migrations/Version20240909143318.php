<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240909143318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe ADD ecole_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9677EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('CREATE INDEX IDX_8F87BF9677EF1B1E ON classe (ecole_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF9677EF1B1E');
        $this->addSql('DROP INDEX IDX_8F87BF9677EF1B1E ON classe');
        $this->addSql('ALTER TABLE classe DROP ecole_id');
    }
}
