<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031065150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_ecole (user_id INT NOT NULL, ecole_id INT NOT NULL, INDEX IDX_EBBA91F4A76ED395 (user_id), INDEX IDX_EBBA91F477EF1B1E (ecole_id), PRIMARY KEY(user_id, ecole_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_ecole ADD CONSTRAINT FK_EBBA91F4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_ecole ADD CONSTRAINT FK_EBBA91F477EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64977EF1B1E');
        $this->addSql('DROP INDEX IDX_8D93D64977EF1B1E ON user');
        $this->addSql('ALTER TABLE user DROP ecole_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_ecole DROP FOREIGN KEY FK_EBBA91F4A76ED395');
        $this->addSql('ALTER TABLE user_ecole DROP FOREIGN KEY FK_EBBA91F477EF1B1E');
        $this->addSql('DROP TABLE user_ecole');
        $this->addSql('ALTER TABLE user ADD ecole_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64977EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64977EF1B1E ON user (ecole_id)');
    }
}
