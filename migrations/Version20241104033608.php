<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104033608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pending_sim_cards ADD type_id INT NOT NULL, ADD imported_csv TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE pending_sim_cards ADD CONSTRAINT FK_7E597F42C54C8C93 FOREIGN KEY (type_id) REFERENCES sim_type (id)');
        $this->addSql('CREATE INDEX IDX_7E597F42C54C8C93 ON pending_sim_cards (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pending_sim_cards DROP FOREIGN KEY FK_7E597F42C54C8C93');
        $this->addSql('DROP INDEX IDX_7E597F42C54C8C93 ON pending_sim_cards');
        $this->addSql('ALTER TABLE pending_sim_cards DROP type_id, DROP imported_csv');
    }
}
