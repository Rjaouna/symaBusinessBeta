<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202181156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE banner (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bonus (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, valeur VARCHAR(50) NOT NULL, motif VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', used TINYINT(1) DEFAULT NULL, INDEX IDX_9F987F7AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carte_sim (id INT AUTO_INCREMENT NOT NULL, purchased_by_id INT DEFAULT NULL, type_id INT NOT NULL, user_id INT DEFAULT NULL, chapelet_id INT NOT NULL, serial_number VARCHAR(19) NOT NULL, reserved TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', etat VARCHAR(50) DEFAULT NULL, usage_finale VARCHAR(50) DEFAULT NULL, canal_vente VARCHAR(50) DEFAULT NULL, INDEX IDX_67D57BF451D43F65 (purchased_by_id), INDEX IDX_67D57BF4C54C8C93 (type_id), INDEX IDX_67D57BF4A76ED395 (user_id), INDEX IDX_67D57BF4DFE7E91 (chapelet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chapelet (id INT AUTO_INCREMENT NOT NULL, type_cartes_id INT NOT NULL, code_chapelet VARCHAR(14) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', reserved TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_BBB259314233211A (code_chapelet), INDEX IDX_BBB25931717C2D42 (type_cartes_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_final (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, telephone VARCHAR(50) NOT NULL, adresse VARCHAR(50) NOT NULL, serial_number VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type_sim_id INT NOT NULL, numero VARCHAR(14) NOT NULL, qte INT NOT NULL, qtevalidee INT DEFAULT NULL, total VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sim_type VARCHAR(50) NOT NULL, code_client VARCHAR(50) NOT NULL, modified TINYINT(1) DEFAULT NULL, qte_bonus_used VARCHAR(10) DEFAULT NULL, factured TINYINT(1) DEFAULT NULL, montant_ht DOUBLE PRECISION NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), INDEX IDX_6EEAA67DDBCD9334 (type_sim_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_settings (id INT AUTO_INCREMENT NOT NULL, confirmation_email_subject VARCHAR(255) NOT NULL, confirmation_email_body LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, numero_facture VARCHAR(14) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', montant_ht DOUBLE PRECISION NOT NULL, montant_tva DOUBLE PRECISION NOT NULL, montant_ttc DOUBLE PRECISION NOT NULL, statut_paiement VARCHAR(50) NOT NULL, mode_paiement VARCHAR(50) NOT NULL, paiement_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', type VARCHAR(10) NOT NULL, seen TINYINT(1) DEFAULT NULL, INDEX IDX_FE86641019EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ligne_facture (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, quantite INT NOT NULL, prix_unitaire_ht DOUBLE PRECISION NOT NULL, montant_total_ht DOUBLE PRECISION NOT NULL, type_carte_sim VARCHAR(20) NOT NULL, INDEX IDX_611F5A297F2DEE08 (facture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lignes_commande (id INT AUTO_INCREMENT NOT NULL, cartes_sims_id INT DEFAULT NULL, commande_id INT NOT NULL, type_sim_id INT DEFAULT NULL, prix_unitaire VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', serial_number VARCHAR(19) NOT NULL, numero_commande VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_DAAE0FCB755D0B32 (cartes_sims_id), INDEX IDX_DAAE0FCB82EA2E54 (commande_id), INDEX IDX_DAAE0FCBDBCD9334 (type_sim_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pending_sim_cards (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, chapelet_id INT NOT NULL, serial_number VARCHAR(19) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', migrated TINYINT(1) NOT NULL, imported_csv TINYINT(1) NOT NULL, INDEX IDX_7E597F42C54C8C93 (type_id), INDEX IDX_7E597F42DFE7E91 (chapelet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quota (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, sim5_quota VARCHAR(50) NOT NULL, sim10_quota VARCHAR(50) NOT NULL, sim15_quota VARCHAR(50) NOT NULL, sim20_quota VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', code VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sim_type (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, prix VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE syma_business_config (id INT AUTO_INCREMENT NOT NULL, nom_du_responsable VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, numero_de_telephone VARCHAR(50) NOT NULL, numero_de_fixe VARCHAR(50) NOT NULL, raison_sociale VARCHAR(50) NOT NULL, numero_de_registre VARCHAR(50) NOT NULL, forme_juridique VARCHAR(50) NOT NULL, code_ape_naf VARCHAR(50) NOT NULL, capital_social VARCHAR(50) NOT NULL, numero_de_tva_intracommunautaire VARCHAR(50) NOT NULL, numero_siret VARCHAR(50) NOT NULL, adresse LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `usage` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type_id INT DEFAULT NULL, sim5_usage VARCHAR(50) DEFAULT NULL, sim10_usage VARCHAR(50) DEFAULT NULL, sim15_usage VARCHAR(50) DEFAULT NULL, sim20_usage VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_D0EB5E70A76ED395 (user_id), INDEX IDX_D0EB5E70C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, quotas_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, nom_responsable VARCHAR(50) NOT NULL, telephone_mobile VARCHAR(10) NOT NULL, nom_societe VARCHAR(50) DEFAULT NULL, numero_siret VARCHAR(50) DEFAULT NULL, adresse VARCHAR(50) DEFAULT NULL, pays VARCHAR(50) DEFAULT NULL, ville VARCHAR(50) DEFAULT NULL, code_client VARCHAR(50) DEFAULT NULL, total_bonus VARCHAR(255) DEFAULT NULL, sim5_usage INT NOT NULL, sim10_usage INT NOT NULL, sim15_usage INT NOT NULL, sim20_usage INT NOT NULL, active_role VARCHAR(255) DEFAULT NULL, last_updated_quota_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', sim5_bonus INT DEFAULT NULL, sim10_bonus INT DEFAULT NULL, sim15_bonus INT DEFAULT NULL, sim20_bonus INT DEFAULT NULL, INDEX IDX_8D93D6494CF462BA (quotas_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bonus ADD CONSTRAINT FK_9F987F7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE carte_sim ADD CONSTRAINT FK_67D57BF451D43F65 FOREIGN KEY (purchased_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE carte_sim ADD CONSTRAINT FK_67D57BF4C54C8C93 FOREIGN KEY (type_id) REFERENCES sim_type (id)');
        $this->addSql('ALTER TABLE carte_sim ADD CONSTRAINT FK_67D57BF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE carte_sim ADD CONSTRAINT FK_67D57BF4DFE7E91 FOREIGN KEY (chapelet_id) REFERENCES chapelet (id)');
        $this->addSql('ALTER TABLE chapelet ADD CONSTRAINT FK_BBB25931717C2D42 FOREIGN KEY (type_cartes_id) REFERENCES sim_type (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DDBCD9334 FOREIGN KEY (type_sim_id) REFERENCES sim_type (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641019EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ligne_facture ADD CONSTRAINT FK_611F5A297F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE lignes_commande ADD CONSTRAINT FK_DAAE0FCB755D0B32 FOREIGN KEY (cartes_sims_id) REFERENCES carte_sim (id)');
        $this->addSql('ALTER TABLE lignes_commande ADD CONSTRAINT FK_DAAE0FCB82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE lignes_commande ADD CONSTRAINT FK_DAAE0FCBDBCD9334 FOREIGN KEY (type_sim_id) REFERENCES sim_type (id)');
        $this->addSql('ALTER TABLE pending_sim_cards ADD CONSTRAINT FK_7E597F42C54C8C93 FOREIGN KEY (type_id) REFERENCES sim_type (id)');
        $this->addSql('ALTER TABLE pending_sim_cards ADD CONSTRAINT FK_7E597F42DFE7E91 FOREIGN KEY (chapelet_id) REFERENCES chapelet (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `usage` ADD CONSTRAINT FK_D0EB5E70A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `usage` ADD CONSTRAINT FK_D0EB5E70C54C8C93 FOREIGN KEY (type_id) REFERENCES sim_type (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494CF462BA FOREIGN KEY (quotas_id) REFERENCES quota (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonus DROP FOREIGN KEY FK_9F987F7AA76ED395');
        $this->addSql('ALTER TABLE carte_sim DROP FOREIGN KEY FK_67D57BF451D43F65');
        $this->addSql('ALTER TABLE carte_sim DROP FOREIGN KEY FK_67D57BF4C54C8C93');
        $this->addSql('ALTER TABLE carte_sim DROP FOREIGN KEY FK_67D57BF4A76ED395');
        $this->addSql('ALTER TABLE carte_sim DROP FOREIGN KEY FK_67D57BF4DFE7E91');
        $this->addSql('ALTER TABLE chapelet DROP FOREIGN KEY FK_BBB25931717C2D42');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DDBCD9334');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE86641019EB6921');
        $this->addSql('ALTER TABLE ligne_facture DROP FOREIGN KEY FK_611F5A297F2DEE08');
        $this->addSql('ALTER TABLE lignes_commande DROP FOREIGN KEY FK_DAAE0FCB755D0B32');
        $this->addSql('ALTER TABLE lignes_commande DROP FOREIGN KEY FK_DAAE0FCB82EA2E54');
        $this->addSql('ALTER TABLE lignes_commande DROP FOREIGN KEY FK_DAAE0FCBDBCD9334');
        $this->addSql('ALTER TABLE pending_sim_cards DROP FOREIGN KEY FK_7E597F42C54C8C93');
        $this->addSql('ALTER TABLE pending_sim_cards DROP FOREIGN KEY FK_7E597F42DFE7E91');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE `usage` DROP FOREIGN KEY FK_D0EB5E70A76ED395');
        $this->addSql('ALTER TABLE `usage` DROP FOREIGN KEY FK_D0EB5E70C54C8C93');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494CF462BA');
        $this->addSql('DROP TABLE banner');
        $this->addSql('DROP TABLE bonus');
        $this->addSql('DROP TABLE carte_sim');
        $this->addSql('DROP TABLE chapelet');
        $this->addSql('DROP TABLE client_final');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE email_settings');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE ligne_facture');
        $this->addSql('DROP TABLE lignes_commande');
        $this->addSql('DROP TABLE pending_sim_cards');
        $this->addSql('DROP TABLE quota');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE sim_type');
        $this->addSql('DROP TABLE syma_business_config');
        $this->addSql('DROP TABLE `usage`');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
