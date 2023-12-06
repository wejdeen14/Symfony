<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231205184415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE amis (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, amis_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_9FE2E761FB88E14F (utilisateur_id), INDEX IDX_9FE2E761706F82C7 (amis_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE amis ADD CONSTRAINT FK_9FE2E761FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE amis ADD CONSTRAINT FK_9FE2E761706F82C7 FOREIGN KEY (amis_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE amis DROP FOREIGN KEY FK_9FE2E761FB88E14F');
        $this->addSql('ALTER TABLE amis DROP FOREIGN KEY FK_9FE2E761706F82C7');
        $this->addSql('DROP TABLE amis');
    }
}
