<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126155156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bed_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, daily_rate NUMERIC(10, 2) DEFAULT NULL, requires_special_care TINYINT(1) DEFAULT 0 NOT NULL, capacity INT DEFAULT 1 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blocking_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, color VARCHAR(30) DEFAULT NULL, requires_approval TINYINT(1) DEFAULT 0 NOT NULL, allows_override TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE branch (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, address LONGTEXT DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, region VARCHAR(100) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE budget_item (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, unit_price NUMERIC(10, 2) DEFAULT NULL, unit_of_measure VARCHAR(50) DEFAULT NULL, category VARCHAR(50) DEFAULT NULL, is_taxable TINYINT(1) DEFAULT 0 NOT NULL, requires_authorization TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cancellation_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, requires_justification TINYINT(1) DEFAULT 0 NOT NULL, allows_refund TINYINT(1) DEFAULT 0 NOT NULL, affects_statistics TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consultation_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, default_duration_minutes INT DEFAULT NULL, requires_prior_appointment TINYINT(1) DEFAULT 0 NOT NULL, is_emergency TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, branch_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_CD1DE18ADCD6CC49 (branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eno_pathology (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, icd10_code VARCHAR(20) DEFAULT NULL, requires_specialist TINYINT(1) DEFAULT 0 NOT NULL, is_chronic TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE external_referrer (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(150) NOT NULL, short_name VARCHAR(50) DEFAULT NULL, referrer_type VARCHAR(100) DEFAULT NULL, rut VARCHAR(20) DEFAULT NULL, address LONGTEXT DEFAULT NULL, contact_person VARCHAR(100) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, has_agreement TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE financier (id INT AUTO_INCREMENT NOT NULL, financier_type_id INT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(150) NOT NULL, short_name VARCHAR(50) DEFAULT NULL, rut VARCHAR(20) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, address LONGTEXT DEFAULT NULL, discount_percentage NUMERIC(5, 2) DEFAULT NULL, payment_days INT DEFAULT NULL, requires_authorization TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F0E352CBFA66F760 (financier_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE financier_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, is_agreement TINYINT(1) DEFAULT 0 NOT NULL, is_default TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ges_pathology (id INT AUTO_INCREMENT NOT NULL, pathology_number VARCHAR(10) DEFAULT NULL, name VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, min_age INT DEFAULT NULL, max_age INT DEFAULT NULL, min_age_months INT DEFAULT NULL, max_age_months INT DEFAULT NULL, gender_restriction VARCHAR(20) DEFAULT NULL, guaranteed_days INT DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requesting_company (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, business_name VARCHAR(150) NOT NULL, trade_name VARCHAR(50) DEFAULT NULL, rut VARCHAR(20) NOT NULL, address LONGTEXT DEFAULT NULL, contact_person VARCHAR(100) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, industry VARCHAR(100) DEFAULT NULL, number_of_employees INT DEFAULT NULL, discount_percentage NUMERIC(5, 2) DEFAULT NULL, payment_term_days INT DEFAULT NULL, has_agreement TINYINT(1) DEFAULT 0 NOT NULL, agreement_start_date DATE DEFAULT NULL, agreement_end_date DATE DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialty (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, category VARCHAR(100) DEFAULT NULL, requires_certification TINYINT(1) DEFAULT 0 NOT NULL, default_consultation_duration INT DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, tax_id VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE treatment_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, requires_specialist TINYINT(1) DEFAULT 0 NOT NULL, requires_authorization TINYINT(1) DEFAULT 0 NOT NULL, average_duration_days INT DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18ADCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE financier ADD CONSTRAINT FK_F0E352CBFA66F760 FOREIGN KEY (financier_type_id) REFERENCES financier_type (id)');
        $this->addSql('DROP INDEX idx_name ON cost_center');
        $this->addSql('DROP INDEX idx_is_active ON cost_center');
        $this->addSql('DROP INDEX idx_code ON cost_center');
        $this->addSql('ALTER TABLE cost_center CHANGE description description LONGTEXT DEFAULT NULL, CHANGE is_active is_active TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX idx_name ON medical_service');
        $this->addSql('DROP INDEX idx_is_active ON medical_service');
        $this->addSql('DROP INDEX idx_code ON medical_service');
        $this->addSql('ALTER TABLE medical_service CHANGE description description LONGTEXT DEFAULT NULL, CHANGE is_active is_active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE medical_service ADD CONSTRAINT FK_A79F7A1CAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE medical_service RENAME INDEX idx_department TO IDX_A79F7A1CAE80F5DF');
        $this->addSql('DROP INDEX idx_code ON service_type');
        $this->addSql('DROP INDEX idx_name ON service_type');
        $this->addSql('DROP INDEX idx_is_active ON service_type');
        $this->addSql('ALTER TABLE service_type ADD requires_authorization TINYINT(1) DEFAULT 0 NOT NULL, ADD requires_bed_assignment TINYINT(1) DEFAULT 0 NOT NULL, ADD default_duration INT DEFAULT NULL, CHANGE name name VARCHAR(100) NOT NULL, CHANGE code code VARCHAR(20) DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medical_service DROP FOREIGN KEY FK_A79F7A1CAE80F5DF');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18ADCD6CC49');
        $this->addSql('ALTER TABLE financier DROP FOREIGN KEY FK_F0E352CBFA66F760');
        $this->addSql('DROP TABLE bed_type');
        $this->addSql('DROP TABLE blocking_type');
        $this->addSql('DROP TABLE branch');
        $this->addSql('DROP TABLE budget_item');
        $this->addSql('DROP TABLE cancellation_type');
        $this->addSql('DROP TABLE consultation_type');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE eno_pathology');
        $this->addSql('DROP TABLE external_referrer');
        $this->addSql('DROP TABLE financier');
        $this->addSql('DROP TABLE financier_type');
        $this->addSql('DROP TABLE ges_pathology');
        $this->addSql('DROP TABLE requesting_company');
        $this->addSql('DROP TABLE specialty');
        $this->addSql('DROP TABLE sub_company');
        $this->addSql('DROP TABLE treatment_type');
        $this->addSql('ALTER TABLE cost_center CHANGE description description TEXT DEFAULT NULL, CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('CREATE INDEX idx_name ON cost_center (name)');
        $this->addSql('CREATE INDEX idx_is_active ON cost_center (is_active)');
        $this->addSql('CREATE INDEX idx_code ON cost_center (code)');
        $this->addSql('ALTER TABLE medical_service CHANGE description description TEXT DEFAULT NULL, CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('CREATE INDEX idx_name ON medical_service (name)');
        $this->addSql('CREATE INDEX idx_is_active ON medical_service (is_active)');
        $this->addSql('CREATE INDEX idx_code ON medical_service (code)');
        $this->addSql('ALTER TABLE medical_service RENAME INDEX idx_a79f7a1cae80f5df TO idx_department');
        $this->addSql('ALTER TABLE service_type DROP requires_authorization, DROP requires_bed_assignment, DROP default_duration, CHANGE code code VARCHAR(100) DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_code ON service_type (code)');
        $this->addSql('CREATE INDEX idx_name ON service_type (name)');
        $this->addSql('CREATE INDEX idx_is_active ON service_type (is_active)');
    }
}
