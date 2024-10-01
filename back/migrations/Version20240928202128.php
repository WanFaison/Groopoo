<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240928202128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annee CHANGE libelle libelle VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE classe CHANGE libelle libelle VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE ecole CHANGE libelle libelle VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE etudiant CHANGE libelle libelle VARCHAR(100) DEFAULT NULL, CHANGE nom nom VARCHAR(50) DEFAULT NULL, CHANGE prenom prenom VARCHAR(100) DEFAULT NULL, CHANGE nationalite nationalite VARCHAR(60) DEFAULT NULL, CHANGE sexe sexe VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE filiere CHANGE libelle libelle VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE groupe CHANGE libelle libelle VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE liste ADD critere JSON DEFAULT NULL, CHANGE libelle libelle VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE niveau CHANGE libelle libelle VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(100) NOT NULL, CHANGE email email VARCHAR(60) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annee CHANGE libelle libelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE classe CHANGE libelle libelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ecole CHANGE libelle libelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE etudiant CHANGE libelle libelle VARCHAR(255) DEFAULT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL, CHANGE nationalite nationalite VARCHAR(255) DEFAULT NULL, CHANGE sexe sexe VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE filiere CHANGE libelle libelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE groupe CHANGE libelle libelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE liste DROP critere, CHANGE libelle libelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE niveau CHANGE libelle libelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(180) NOT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
    }
}
