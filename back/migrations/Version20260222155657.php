<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222155657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE absence (id INT AUTO_INCREMENT NOT NULL, jour_id INT DEFAULT NULL, etudiant_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, type VARCHAR(255) DEFAULT NULL, nbre_hr INT DEFAULT NULL, INDEX IDX_765AE0C9220C6AD0 (jour_id), INDEX IDX_765AE0C9DDEAB1A3 (etudiant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE annee (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, filiere_id INT NOT NULL, niveau_id INT NOT NULL, ecole_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, effectif INT NOT NULL, INDEX IDX_8F87BF96180AA129 (filiere_id), INDEX IDX_8F87BF96B3E9C81 (niveau_id), INDEX IDX_8F87BF9677EF1B1E (ecole_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ecole (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etage (id INT AUTO_INCREMENT NOT NULL, ecole_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, INDEX IDX_2DDCF14B77EF1B1E (ecole_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etudiant (id INT AUTO_INCREMENT NOT NULL, groupe_id INT DEFAULT NULL, classe_id INT NOT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, matricule VARCHAR(255) NOT NULL, nom VARCHAR(50) DEFAULT NULL, prenom VARCHAR(100) DEFAULT NULL, nationalite VARCHAR(60) DEFAULT NULL, sexe VARCHAR(10) DEFAULT NULL, note_etd DOUBLE PRECISION DEFAULT NULL, note_final DOUBLE PRECISION DEFAULT NULL, INDEX IDX_717E22E37A45358C (groupe_id), INDEX IDX_717E22E38F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filiere (id INT AUTO_INCREMENT NOT NULL, ecole_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, INDEX IDX_2ED05D9E77EF1B1E (ecole_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, liste_id INT NOT NULL, salle_id INT DEFAULT NULL, coach_id INT DEFAULT NULL, jury_id INT DEFAULT NULL, theme_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, taille INT NOT NULL, note DOUBLE PRECISION DEFAULT NULL, is_final TINYINT(1) NOT NULL, INDEX IDX_4B98C21E85441D8 (liste_id), INDEX IDX_4B98C21DC304035 (salle_id), INDEX IDX_4B98C213C105691 (coach_id), INDEX IDX_4B98C21E560103C (jury_id), INDEX IDX_4B98C2159027487 (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jour (id INT AUTO_INCREMENT NOT NULL, liste_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, date DATE DEFAULT NULL, INDEX IDX_DA17D9C5E85441D8 (liste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jury (id INT AUTO_INCREMENT NOT NULL, liste_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, effectif INT DEFAULT NULL, is_final TINYINT(1) NOT NULL, INDEX IDX_1335B02CE85441D8 (liste_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE liste (id INT AUTO_INCREMENT NOT NULL, annee_id INT NOT NULL, ecole_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, is_complete TINYINT(1) NOT NULL, date DATE DEFAULT NULL, critere JSON DEFAULT NULL, is_imported TINYINT(1) NOT NULL, notes JSON DEFAULT NULL, INDEX IDX_FCF22AF4543EC5F0 (annee_id), INDEX IDX_FCF22AF477EF1B1E (ecole_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE niveau (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, etage_id INT DEFAULT NULL, ecole_id INT DEFAULT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, INDEX IDX_4E977E5C984CE93F (etage_id), INDEX IDX_4E977E5C77EF1B1E (ecole_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(100) NOT NULL, etat VARCHAR(255) DEFAULT NULL, nom VARCHAR(50) DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(60) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_ecole (user_id INT NOT NULL, ecole_id INT NOT NULL, INDEX IDX_EBBA91F4A76ED395 (user_id), INDEX IDX_EBBA91F477EF1B1E (ecole_id), PRIMARY KEY(user_id, ecole_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_jury (user_id INT NOT NULL, jury_id INT NOT NULL, INDEX IDX_69B4FCE5A76ED395 (user_id), INDEX IDX_69B4FCE5E560103C (jury_id), PRIMARY KEY(user_id, jury_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_liste (user_id INT NOT NULL, liste_id INT NOT NULL, INDEX IDX_1E30D1ACA76ED395 (user_id), INDEX IDX_1E30D1ACE85441D8 (liste_id), PRIMARY KEY(user_id, liste_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C9220C6AD0 FOREIGN KEY (jour_id) REFERENCES jour (id)');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C9DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96B3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9677EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('ALTER TABLE etage ADD CONSTRAINT FK_2DDCF14B77EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E37A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E38F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE filiere ADD CONSTRAINT FK_2ED05D9E77EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21E85441D8 FOREIGN KEY (liste_id) REFERENCES liste (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C213C105691 FOREIGN KEY (coach_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21E560103C FOREIGN KEY (jury_id) REFERENCES jury (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C2159027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE jour ADD CONSTRAINT FK_DA17D9C5E85441D8 FOREIGN KEY (liste_id) REFERENCES liste (id)');
        $this->addSql('ALTER TABLE jury ADD CONSTRAINT FK_1335B02CE85441D8 FOREIGN KEY (liste_id) REFERENCES liste (id)');
        $this->addSql('ALTER TABLE liste ADD CONSTRAINT FK_FCF22AF4543EC5F0 FOREIGN KEY (annee_id) REFERENCES annee (id)');
        $this->addSql('ALTER TABLE liste ADD CONSTRAINT FK_FCF22AF477EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5C984CE93F FOREIGN KEY (etage_id) REFERENCES etage (id)');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5C77EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)');
        $this->addSql('ALTER TABLE user_ecole ADD CONSTRAINT FK_EBBA91F4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_ecole ADD CONSTRAINT FK_EBBA91F477EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_jury ADD CONSTRAINT FK_69B4FCE5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_jury ADD CONSTRAINT FK_69B4FCE5E560103C FOREIGN KEY (jury_id) REFERENCES jury (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_liste ADD CONSTRAINT FK_1E30D1ACA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_liste ADD CONSTRAINT FK_1E30D1ACE85441D8 FOREIGN KEY (liste_id) REFERENCES liste (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C9220C6AD0');
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C9DDEAB1A3');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96180AA129');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96B3E9C81');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF9677EF1B1E');
        $this->addSql('ALTER TABLE etage DROP FOREIGN KEY FK_2DDCF14B77EF1B1E');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E37A45358C');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E38F5EA509');
        $this->addSql('ALTER TABLE filiere DROP FOREIGN KEY FK_2ED05D9E77EF1B1E');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21E85441D8');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21DC304035');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C213C105691');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21E560103C');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C2159027487');
        $this->addSql('ALTER TABLE jour DROP FOREIGN KEY FK_DA17D9C5E85441D8');
        $this->addSql('ALTER TABLE jury DROP FOREIGN KEY FK_1335B02CE85441D8');
        $this->addSql('ALTER TABLE liste DROP FOREIGN KEY FK_FCF22AF4543EC5F0');
        $this->addSql('ALTER TABLE liste DROP FOREIGN KEY FK_FCF22AF477EF1B1E');
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5C984CE93F');
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5C77EF1B1E');
        $this->addSql('ALTER TABLE user_ecole DROP FOREIGN KEY FK_EBBA91F4A76ED395');
        $this->addSql('ALTER TABLE user_ecole DROP FOREIGN KEY FK_EBBA91F477EF1B1E');
        $this->addSql('ALTER TABLE user_jury DROP FOREIGN KEY FK_69B4FCE5A76ED395');
        $this->addSql('ALTER TABLE user_jury DROP FOREIGN KEY FK_69B4FCE5E560103C');
        $this->addSql('ALTER TABLE user_liste DROP FOREIGN KEY FK_1E30D1ACA76ED395');
        $this->addSql('ALTER TABLE user_liste DROP FOREIGN KEY FK_1E30D1ACE85441D8');
        $this->addSql('DROP TABLE absence');
        $this->addSql('DROP TABLE annee');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE ecole');
        $this->addSql('DROP TABLE etage');
        $this->addSql('DROP TABLE etudiant');
        $this->addSql('DROP TABLE filiere');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE jour');
        $this->addSql('DROP TABLE jury');
        $this->addSql('DROP TABLE liste');
        $this->addSql('DROP TABLE niveau');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_ecole');
        $this->addSql('DROP TABLE user_jury');
        $this->addSql('DROP TABLE user_liste');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
