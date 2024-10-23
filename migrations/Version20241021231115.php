<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021231115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_sim ADD type_id INT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE carte_sim ADD CONSTRAINT FK_67D57BF4C54C8C93 FOREIGN KEY (type_id) REFERENCES sim_type (id)');
        $this->addSql('CREATE INDEX IDX_67D57BF4C54C8C93 ON carte_sim (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_sim DROP FOREIGN KEY FK_67D57BF4C54C8C93');
        $this->addSql('DROP INDEX IDX_67D57BF4C54C8C93 ON carte_sim');
        $this->addSql('ALTER TABLE carte_sim DROP type_id, DROP created_at, DROP updated_at');
    }
}
