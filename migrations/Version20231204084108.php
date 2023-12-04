<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204084108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE amis ADD amis_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE amis ADD CONSTRAINT FK_9FE2E7612619C385 FOREIGN KEY (amis_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9FE2E7612619C385 ON amis (amis_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE amis DROP FOREIGN KEY FK_9FE2E7612619C385');
        $this->addSql('DROP INDEX IDX_9FE2E7612619C385 ON amis');
        $this->addSql('ALTER TABLE amis DROP amis_id_id');
    }
}
