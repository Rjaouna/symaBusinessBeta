<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241112223641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_facture DROP FOREIGN KEY FK_611F5A29F347EFB');
        $this->addSql('DROP INDEX IDX_611F5A29F347EFB ON ligne_facture');
        $this->addSql('ALTER TABLE ligne_facture DROP produit_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_facture ADD produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_facture ADD CONSTRAINT FK_611F5A29F347EFB FOREIGN KEY (produit_id) REFERENCES carte_sim (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_611F5A29F347EFB ON ligne_facture (produit_id)');
    }
}
