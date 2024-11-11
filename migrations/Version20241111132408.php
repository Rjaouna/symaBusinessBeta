<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241111132408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chapelet (id INT AUTO_INCREMENT NOT NULL, code_chapelet VARCHAR(14) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carte_sim ADD chapelet_id INT NOT NULL');
        $this->addSql('ALTER TABLE carte_sim ADD CONSTRAINT FK_67D57BF4DFE7E91 FOREIGN KEY (chapelet_id) REFERENCES chapelet (id)');
        $this->addSql('CREATE INDEX IDX_67D57BF4DFE7E91 ON carte_sim (chapelet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_sim DROP FOREIGN KEY FK_67D57BF4DFE7E91');
        $this->addSql('DROP TABLE chapelet');
        $this->addSql('DROP INDEX IDX_67D57BF4DFE7E91 ON carte_sim');
        $this->addSql('ALTER TABLE carte_sim DROP chapelet_id');
    }
}
