<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021230745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carte_sim (id INT AUTO_INCREMENT NOT NULL, purchased_by_id INT DEFAULT NULL, serial_number VARCHAR(14) NOT NULL, reserved TINYINT(1) NOT NULL, INDEX IDX_67D57BF451D43F65 (purchased_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sim_type (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, prix VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carte_sim ADD CONSTRAINT FK_67D57BF451D43F65 FOREIGN KEY (purchased_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_sim DROP FOREIGN KEY FK_67D57BF451D43F65');
        $this->addSql('DROP TABLE carte_sim');
        $this->addSql('DROP TABLE sim_type');
    }
}
