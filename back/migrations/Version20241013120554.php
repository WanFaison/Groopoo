<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241013120554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405CCFA12B8');
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405A76ED395');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE user_profile');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, is_archived TINYINT(1) NOT NULL, authorizations JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_profile (user_id INT NOT NULL, profile_id INT NOT NULL, INDEX IDX_D95AB405A76ED395 (user_id), INDEX IDX_D95AB405CCFA12B8 (profile_id), PRIMARY KEY(user_id, profile_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}
