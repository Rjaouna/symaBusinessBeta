<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021165812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonus ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bonus ADD CONSTRAINT FK_9F987F7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F987F7AA76ED395 ON bonus (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonus DROP FOREIGN KEY FK_9F987F7AA76ED395');
        $this->addSql('DROP INDEX UNIQ_9F987F7AA76ED395 ON bonus');
        $this->addSql('ALTER TABLE bonus DROP user_id');
    }
}
