<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202181322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `usage` DROP sim5_usage, DROP sim10_usage, DROP sim15_usage, DROP sim20_usage');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `usage` ADD sim5_usage VARCHAR(50) DEFAULT NULL, ADD sim10_usage VARCHAR(50) DEFAULT NULL, ADD sim15_usage VARCHAR(50) DEFAULT NULL, ADD sim20_usage VARCHAR(50) DEFAULT NULL');
    }
}
