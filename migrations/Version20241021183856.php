<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021183856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonus DROP INDEX UNIQ_9F987F7AA76ED395, ADD INDEX IDX_9F987F7AA76ED395 (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64969545666');
        $this->addSql('DROP INDEX UNIQ_8D93D64969545666 ON user');
        $this->addSql('ALTER TABLE user DROP bonus_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonus DROP INDEX IDX_9F987F7AA76ED395, ADD UNIQUE INDEX UNIQ_9F987F7AA76ED395 (user_id)');
        $this->addSql('ALTER TABLE user ADD bonus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64969545666 FOREIGN KEY (bonus_id) REFERENCES bonus (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64969545666 ON user (bonus_id)');
    }
}
