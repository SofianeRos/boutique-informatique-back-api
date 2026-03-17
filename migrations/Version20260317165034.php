<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260317165034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sav (id INT AUTO_INCREMENT NOT NULL, materiel_nom VARCHAR(255) NOT NULL, description_panne LONGTEXT NOT NULL, statut VARCHAR(255) NOT NULL, user_id INT NOT NULL, INDEX IDX_6C7681F4A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE sav ADD CONSTRAINT FK_6C7681F4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sav DROP FOREIGN KEY FK_6C7681F4A76ED395');
        $this->addSql('DROP TABLE sav');
    }
}
