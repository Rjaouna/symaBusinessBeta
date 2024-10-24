<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023223606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_sim ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carte_sim ADD CONSTRAINT FK_67D57BF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_67D57BF4A76ED395 ON carte_sim (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_sim DROP FOREIGN KEY FK_67D57BF4A76ED395');
        $this->addSql('DROP INDEX IDX_67D57BF4A76ED395 ON carte_sim');
        $this->addSql('ALTER TABLE carte_sim DROP user_id');
    }
}
