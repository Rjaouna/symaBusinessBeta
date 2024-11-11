<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241108215650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD qte_bonus_used VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP facade, DROP code_postal, DROP iban, DROP bic');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP qte_bonus_used');
        $this->addSql('ALTER TABLE user ADD facade VARCHAR(255) DEFAULT NULL, ADD code_postal VARCHAR(5) DEFAULT NULL, ADD iban VARCHAR(50) DEFAULT NULL, ADD bic VARCHAR(50) DEFAULT NULL');
    }
}
