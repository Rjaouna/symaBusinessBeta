<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021143419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD nom_responsable VARCHAR(50) NOT NULL, ADD telephone_fixe VARCHAR(10) NOT NULL, ADD telephone_mobile VARCHAR(10) NOT NULL, ADD nom_societe VARCHAR(50) DEFAULT NULL, ADD forme_juridique VARCHAR(50) DEFAULT NULL, ADD numero_registre_commerce VARCHAR(50) DEFAULT NULL, ADD numero_siret VARCHAR(50) DEFAULT NULL, ADD numero_rcs VARCHAR(50) DEFAULT NULL, ADD code_ape VARCHAR(50) DEFAULT NULL, ADD facade VARCHAR(255) DEFAULT NULL, ADD kbis VARCHAR(50) DEFAULT NULL, ADD adresse VARCHAR(50) DEFAULT NULL, ADD pays VARCHAR(50) DEFAULT NULL, ADD code_postal VARCHAR(5) DEFAULT NULL, ADD ville VARCHAR(50) DEFAULT NULL, ADD code_client VARCHAR(50) DEFAULT NULL, ADD iban VARCHAR(50) DEFAULT NULL, ADD bic VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP nom_responsable, DROP telephone_fixe, DROP telephone_mobile, DROP nom_societe, DROP forme_juridique, DROP numero_registre_commerce, DROP numero_siret, DROP numero_rcs, DROP code_ape, DROP facade, DROP kbis, DROP adresse, DROP pays, DROP code_postal, DROP ville, DROP code_client, DROP iban, DROP bic');
    }
}
