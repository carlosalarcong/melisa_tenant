<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251230203352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank (id INT AUTO_INCREMENT NOT NULL, tax_id VARCHAR(50) DEFAULT NULL, name VARCHAR(255) NOT NULL, account_number BIGINT DEFAULT NULL, state_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit_card (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) DEFAULT NULL, abbreviation VARCHAR(50) DEFAULT NULL, credit_card_type_id INT DEFAULT NULL, state_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit_card_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, state_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE free_charge_reason (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, state_id INT DEFAULT NULL, branch_id INT DEFAULT NULL, type_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE insurance_plan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, abbreviated_name VARCHAR(45) DEFAULT NULL, plan_code INT DEFAULT NULL, interface_code VARCHAR(100) DEFAULT NULL, copayment_amount INT DEFAULT NULL, icon VARCHAR(45) DEFAULT NULL, is_default TINYINT(1) NOT NULL, state_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, insurance_type_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_adjustment_direction (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_adjustment_reason (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, state_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, payment_adjustment_direction_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_condition (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, interface_code VARCHAR(10) NOT NULL, max_term INT NOT NULL, is_on_day TINYINT(1) NOT NULL, state_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referral_source (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, branch_id INT DEFAULT NULL, referral_source_type_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE treatment_type (id INT AUTO_INCREMENT NOT NULL, code INT NOT NULL, name VARCHAR(255) NOT NULL, is_imed TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, organization_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE pais');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pais (id INT AUTO_INCREMENT NOT NULL, nombre_pais VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, nombre_gentilicio VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, activo TINYINT(1) DEFAULT 1 NOT NULL, id_estado TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE bank');
        $this->addSql('DROP TABLE credit_card');
        $this->addSql('DROP TABLE credit_card_type');
        $this->addSql('DROP TABLE free_charge_reason');
        $this->addSql('DROP TABLE insurance_plan');
        $this->addSql('DROP TABLE payment_adjustment_direction');
        $this->addSql('DROP TABLE payment_adjustment_reason');
        $this->addSql('DROP TABLE payment_condition');
        $this->addSql('DROP TABLE referral_source');
        $this->addSql('DROP TABLE treatment_type');
    }
}
