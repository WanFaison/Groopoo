<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128120703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE salle ADD ecole_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5C77EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('CREATE INDEX IDX_4E977E5C77EF1B1E ON salle (ecole_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5C77EF1B1E');
        $this->addSql('DROP INDEX IDX_4E977E5C77EF1B1E ON salle');
        $this->addSql('ALTER TABLE salle DROP ecole_id');
    }
}
