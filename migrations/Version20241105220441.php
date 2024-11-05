<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105220441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP telephone_fixe, DROP forme_juridique, DROP numero_registre_commerce, DROP numero_rcs, DROP code_ape, DROP kbis');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD telephone_fixe VARCHAR(10) NOT NULL, ADD forme_juridique VARCHAR(50) DEFAULT NULL, ADD numero_registre_commerce VARCHAR(50) DEFAULT NULL, ADD numero_rcs VARCHAR(50) DEFAULT NULL, ADD code_ape VARCHAR(50) DEFAULT NULL, ADD kbis VARCHAR(50) DEFAULT NULL');
    }
}
