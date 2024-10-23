<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023180818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lignes_commande ADD type_sim_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lignes_commande ADD CONSTRAINT FK_DAAE0FCBDBCD9334 FOREIGN KEY (type_sim_id) REFERENCES sim_type (id)');
        $this->addSql('CREATE INDEX IDX_DAAE0FCBDBCD9334 ON lignes_commande (type_sim_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lignes_commande DROP FOREIGN KEY FK_DAAE0FCBDBCD9334');
        $this->addSql('DROP INDEX IDX_DAAE0FCBDBCD9334 ON lignes_commande');
        $this->addSql('ALTER TABLE lignes_commande DROP type_sim_id');
    }
}
