<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119143629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipes (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, logo_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joueurs (id INT AUTO_INCREMENT NOT NULL, equipe_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, INDEX IDX_F0FD889D6D861B89 (equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matchs (id INT AUTO_INCREMENT NOT NULL, equipe_a_id INT NOT NULL, equipe_b_id INT NOT NULL, date DATETIME DEFAULT NULL, score_a SMALLINT DEFAULT NULL, score_b SMALLINT DEFAULT NULL, INDEX IDX_6B1E60413297C2A6 (equipe_a_id), INDEX IDX_6B1E604120226D48 (equipe_b_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE joueurs ADD CONSTRAINT FK_F0FD889D6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipes (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E60413297C2A6 FOREIGN KEY (equipe_a_id) REFERENCES equipes (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E604120226D48 FOREIGN KEY (equipe_b_id) REFERENCES equipes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE joueurs DROP FOREIGN KEY FK_F0FD889D6D861B89');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E60413297C2A6');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E604120226D48');
        $this->addSql('DROP TABLE equipes');
        $this->addSql('DROP TABLE joueurs');
        $this->addSql('DROP TABLE matchs');
    }
}
