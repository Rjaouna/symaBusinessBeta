<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021152656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bonus (id INT AUTO_INCREMENT NOT NULL, valeur VARCHAR(50) NOT NULL, motif VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_final (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, telephone VARCHAR(50) NOT NULL, adresse VARCHAR(50) NOT NULL, serial_number VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, numero VARCHAR(10) NOT NULL, qte INT NOT NULL, qtevalidee INT DEFAULT NULL, total VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sim_type VARCHAR(50) NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quota (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, sim5_quota VARCHAR(50) NOT NULL, sim10_quota VARCHAR(50) NOT NULL, sim15_quota VARCHAR(50) NOT NULL, sim20_quota VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `usage` (id INT AUTO_INCREMENT NOT NULL, sim5_usage VARCHAR(50) DEFAULT NULL, sim10_usage VARCHAR(50) DEFAULT NULL, sim15_usage VARCHAR(50) DEFAULT NULL, sim20_usage VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD quotas_id INT DEFAULT NULL, ADD usages_id INT DEFAULT NULL, ADD bonus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494CF462BA FOREIGN KEY (quotas_id) REFERENCES quota (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F78A9799 FOREIGN KEY (usages_id) REFERENCES `usage` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64969545666 FOREIGN KEY (bonus_id) REFERENCES bonus (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494CF462BA ON user (quotas_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F78A9799 ON user (usages_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64969545666 ON user (bonus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64969545666');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494CF462BA');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F78A9799');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('DROP TABLE bonus');
        $this->addSql('DROP TABLE client_final');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE quota');
        $this->addSql('DROP TABLE `usage`');
        $this->addSql('DROP INDEX IDX_8D93D6494CF462BA ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649F78A9799 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D64969545666 ON user');
        $this->addSql('ALTER TABLE user DROP quotas_id, DROP usages_id, DROP bonus_id');
    }
}
