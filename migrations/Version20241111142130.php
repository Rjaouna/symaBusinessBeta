<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241111142130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pending_sim_cards ADD chapelet_id INT NOT NULL');
        $this->addSql('ALTER TABLE pending_sim_cards ADD CONSTRAINT FK_7E597F42DFE7E91 FOREIGN KEY (chapelet_id) REFERENCES chapelet (id)');
        $this->addSql('CREATE INDEX IDX_7E597F42DFE7E91 ON pending_sim_cards (chapelet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pending_sim_cards DROP FOREIGN KEY FK_7E597F42DFE7E91');
        $this->addSql('DROP INDEX IDX_7E597F42DFE7E91 ON pending_sim_cards');
        $this->addSql('ALTER TABLE pending_sim_cards DROP chapelet_id');
    }
}
