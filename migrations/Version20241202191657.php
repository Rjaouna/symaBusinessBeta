<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202191657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE limitation ADD type_carte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE limitation ADD CONSTRAINT FK_5284EA52438E94A FOREIGN KEY (type_carte_id) REFERENCES sim_type (id)');
        $this->addSql('CREATE INDEX IDX_5284EA52438E94A ON limitation (type_carte_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `limitation` DROP FOREIGN KEY FK_5284EA52438E94A');
        $this->addSql('DROP INDEX IDX_5284EA52438E94A ON `limitation`');
        $this->addSql('ALTER TABLE `limitation` DROP type_carte_id');
    }
}
