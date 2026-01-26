<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126163336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agreement DROP FOREIGN KEY FK_2E655A244A353075');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8444A353075');
        $this->addSql('ALTER TABLE authorization DROP FOREIGN KEY FK_7A6D8BEF4A353075');
        $this->addSql('ALTER TABLE professional_rate DROP FOREIGN KEY FK_CDA906E74A353075');
        $this->addSql('CREATE TABLE branch_payer (id INT AUTO_INCREMENT NOT NULL, branch_id INT NOT NULL, payer_id INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2F7A59CEDCD6CC49 (branch_id), INDEX IDX_2F7A59CEC17AD9A9 (payer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE branch_service_type (id INT AUTO_INCREMENT NOT NULL, branch_id INT NOT NULL, service_type_id INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1F793463DCD6CC49 (branch_id), INDEX IDX_1F793463AC8DE0F (service_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exam_package (id INT AUTO_INCREMENT NOT NULL, medical_service_id INT DEFAULT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, is_billable TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D3CE3EFDC61D802A (medical_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exam_package_detail (id INT AUTO_INCREMENT NOT NULL, exam_package_id INT NOT NULL, medical_service_id INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8E28E519E644D1AA (exam_package_id), INDEX IDX_8E28E519C61D802A (medical_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE figure (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, is_surgical_room TINYINT(1) NOT NULL, is_zero TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medical_service_bed_type (id INT AUTO_INCREMENT NOT NULL, medical_service_id INT NOT NULL, bed_type_id INT NOT NULL, quantity INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_EEE3D850C61D802A (medical_service_id), INDEX IDX_EEE3D8508158330E (bed_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medical_service_budget_item (id INT AUTO_INCREMENT NOT NULL, medical_service_id INT NOT NULL, surgery_item_id INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9E9803FBC61D802A (medical_service_id), INDEX IDX_9E9803FBA4665449 (surgery_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medical_service_service (id INT AUTO_INCREMENT NOT NULL, medical_service_id INT NOT NULL, service_id INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D8EE37BEC61D802A (medical_service_id), INDEX IDX_D8EE37BEED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payer (id INT AUTO_INCREMENT NOT NULL, payer_type_id INT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(150) NOT NULL, short_name VARCHAR(50) DEFAULT NULL, rut VARCHAR(20) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, address LONGTEXT DEFAULT NULL, discount_percentage NUMERIC(5, 2) DEFAULT NULL, payment_days INT DEFAULT NULL, requires_authorization TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_41CB5B99BEA5E0F7 (payer_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payer_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, is_agreement TINYINT(1) DEFAULT 0 NOT NULL, is_default TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_package (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, is_billable TINYINT(1) NOT NULL, is_program TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_package_detail (id INT AUTO_INCREMENT NOT NULL, service_package_id INT NOT NULL, medical_service_id INT NOT NULL, quantity INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_139B51F9621D924B (service_package_id), INDEX IDX_139B51F9C61D802A (medical_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialty_branch (id INT AUTO_INCREMENT NOT NULL, specialty_id INT NOT NULL, branch_id INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_89FD01D99A353316 (specialty_id), INDEX IDX_89FD01D9DCD6CC49 (branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse (id INT AUTO_INCREMENT NOT NULL, service_id INT DEFAULT NULL, parent_warehouse_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, short_name VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, creates_article TINYINT(1) NOT NULL, is_virtual TINYINT(1) NOT NULL, is_pharmacy TINYINT(1) NOT NULL, is_automatic_reception TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_ECB38BFCED5CA9E6 (service_id), INDEX IDX_ECB38BFCEEB1F0 (parent_warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_medical_service (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT NOT NULL, medical_service_id INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E698EAF5080ECDE (warehouse_id), INDEX IDX_E698EAFC61D802A (medical_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE branch_payer ADD CONSTRAINT FK_2F7A59CEDCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE branch_payer ADD CONSTRAINT FK_2F7A59CEC17AD9A9 FOREIGN KEY (payer_id) REFERENCES payer (id)');
        $this->addSql('ALTER TABLE branch_service_type ADD CONSTRAINT FK_1F793463DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE branch_service_type ADD CONSTRAINT FK_1F793463AC8DE0F FOREIGN KEY (service_type_id) REFERENCES service_type (id)');
        $this->addSql('ALTER TABLE exam_package ADD CONSTRAINT FK_D3CE3EFDC61D802A FOREIGN KEY (medical_service_id) REFERENCES medical_service (id)');
        $this->addSql('ALTER TABLE exam_package_detail ADD CONSTRAINT FK_8E28E519E644D1AA FOREIGN KEY (exam_package_id) REFERENCES exam_package (id)');
        $this->addSql('ALTER TABLE exam_package_detail ADD CONSTRAINT FK_8E28E519C61D802A FOREIGN KEY (medical_service_id) REFERENCES medical_service (id)');
        $this->addSql('ALTER TABLE medical_service_bed_type ADD CONSTRAINT FK_EEE3D850C61D802A FOREIGN KEY (medical_service_id) REFERENCES medical_service (id)');
        $this->addSql('ALTER TABLE medical_service_bed_type ADD CONSTRAINT FK_EEE3D8508158330E FOREIGN KEY (bed_type_id) REFERENCES bed_type (id)');
        $this->addSql('ALTER TABLE medical_service_budget_item ADD CONSTRAINT FK_9E9803FBC61D802A FOREIGN KEY (medical_service_id) REFERENCES medical_service (id)');
        $this->addSql('ALTER TABLE medical_service_budget_item ADD CONSTRAINT FK_9E9803FBA4665449 FOREIGN KEY (surgery_item_id) REFERENCES surgery_item (id)');
        $this->addSql('ALTER TABLE medical_service_service ADD CONSTRAINT FK_D8EE37BEC61D802A FOREIGN KEY (medical_service_id) REFERENCES medical_service (id)');
        $this->addSql('ALTER TABLE medical_service_service ADD CONSTRAINT FK_D8EE37BEED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE payer ADD CONSTRAINT FK_41CB5B99BEA5E0F7 FOREIGN KEY (payer_type_id) REFERENCES payer_type (id)');
        $this->addSql('ALTER TABLE service_package_detail ADD CONSTRAINT FK_139B51F9621D924B FOREIGN KEY (service_package_id) REFERENCES service_package (id)');
        $this->addSql('ALTER TABLE service_package_detail ADD CONSTRAINT FK_139B51F9C61D802A FOREIGN KEY (medical_service_id) REFERENCES medical_service (id)');
        $this->addSql('ALTER TABLE specialty_branch ADD CONSTRAINT FK_89FD01D99A353316 FOREIGN KEY (specialty_id) REFERENCES specialty (id)');
        $this->addSql('ALTER TABLE specialty_branch ADD CONSTRAINT FK_89FD01D9DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE warehouse ADD CONSTRAINT FK_ECB38BFCED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE warehouse ADD CONSTRAINT FK_ECB38BFCEEB1F0 FOREIGN KEY (parent_warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE warehouse_medical_service ADD CONSTRAINT FK_E698EAF5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE warehouse_medical_service ADD CONSTRAINT FK_E698EAFC61D802A FOREIGN KEY (medical_service_id) REFERENCES medical_service (id)');
        $this->addSql('ALTER TABLE financier DROP FOREIGN KEY FK_F0E352CBFA66F760');
        $this->addSql('DROP TABLE financier');
        $this->addSql('DROP TABLE financier_type');
        $this->addSql('DROP INDEX IDX_2E655A244A353075 ON agreement');
        $this->addSql('ALTER TABLE agreement CHANGE financier_id payer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agreement ADD CONSTRAINT FK_2E655A24C17AD9A9 FOREIGN KEY (payer_id) REFERENCES payer (id)');
        $this->addSql('CREATE INDEX IDX_2E655A24C17AD9A9 ON agreement (payer_id)');
        $this->addSql('DROP INDEX IDX_FE38F8444A353075 ON appointment');
        $this->addSql('ALTER TABLE appointment CHANGE financier_id payer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844C17AD9A9 FOREIGN KEY (payer_id) REFERENCES payer (id)');
        $this->addSql('CREATE INDEX IDX_FE38F844C17AD9A9 ON appointment (payer_id)');
        $this->addSql('DROP INDEX IDX_7A6D8BEF4A353075 ON authorization');
        $this->addSql('ALTER TABLE authorization CHANGE financier_id payer_id INT NOT NULL');
        $this->addSql('ALTER TABLE authorization ADD CONSTRAINT FK_7A6D8BEFC17AD9A9 FOREIGN KEY (payer_id) REFERENCES payer (id)');
        $this->addSql('CREATE INDEX IDX_7A6D8BEFC17AD9A9 ON authorization (payer_id)');
        $this->addSql('ALTER TABLE medical_service DROP FOREIGN KEY FK_A79F7A1CAE80F5DF');
        $this->addSql('DROP INDEX IDX_A79F7A1CAE80F5DF ON medical_service');
        $this->addSql('ALTER TABLE medical_service ADD budget_item_id INT DEFAULT NULL, ADD service_type_id INT DEFAULT NULL, ADD sub_company_id INT DEFAULT NULL, ADD billing_sub_company_id INT DEFAULT NULL, ADD fonasa_code VARCHAR(20) DEFAULT NULL, ADD short_name VARCHAR(45) DEFAULT NULL, ADD accounting_code_erp INT DEFAULT NULL, ADD duration TIME DEFAULT NULL, ADD applies_surcharge TINYINT(1) NOT NULL, ADD is_imed TINYINT(1) NOT NULL, ADD imed_code VARCHAR(20) DEFAULT NULL, ADD interface_code VARCHAR(20) DEFAULT NULL, ADD participation INT DEFAULT NULL, ADD is_taxed TINYINT(1) NOT NULL, ADD is_procedure TINYINT(1) NOT NULL, DROP hl7_service_type, CHANGE name name LONGTEXT NOT NULL, CHANGE code code VARCHAR(20) NOT NULL, CHANGE department_id figure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE medical_service ADD CONSTRAINT FK_A79F7A1C5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('ALTER TABLE medical_service ADD CONSTRAINT FK_A79F7A1CD0EC18BF FOREIGN KEY (budget_item_id) REFERENCES budget_item (id)');
        $this->addSql('ALTER TABLE medical_service ADD CONSTRAINT FK_A79F7A1CAC8DE0F FOREIGN KEY (service_type_id) REFERENCES service_type (id)');
        $this->addSql('ALTER TABLE medical_service ADD CONSTRAINT FK_A79F7A1CBB50891B FOREIGN KEY (sub_company_id) REFERENCES sub_company (id)');
        $this->addSql('ALTER TABLE medical_service ADD CONSTRAINT FK_A79F7A1C3395B1AC FOREIGN KEY (billing_sub_company_id) REFERENCES sub_company (id)');
        $this->addSql('CREATE INDEX IDX_A79F7A1C5C011B5 ON medical_service (figure_id)');
        $this->addSql('CREATE INDEX IDX_A79F7A1CD0EC18BF ON medical_service (budget_item_id)');
        $this->addSql('CREATE INDEX IDX_A79F7A1CAC8DE0F ON medical_service (service_type_id)');
        $this->addSql('CREATE INDEX IDX_A79F7A1CBB50891B ON medical_service (sub_company_id)');
        $this->addSql('CREATE INDEX IDX_A79F7A1C3395B1AC ON medical_service (billing_sub_company_id)');
        $this->addSql('DROP INDEX IDX_CDA906E74A353075 ON professional_rate');
        $this->addSql('ALTER TABLE professional_rate CHANGE financier_id payer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE professional_rate ADD CONSTRAINT FK_CDA906E7C17AD9A9 FOREIGN KEY (payer_id) REFERENCES payer (id)');
        $this->addSql('CREATE INDEX IDX_CDA906E7C17AD9A9 ON professional_rate (payer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medical_service DROP FOREIGN KEY FK_A79F7A1C5C011B5');
        $this->addSql('ALTER TABLE agreement DROP FOREIGN KEY FK_2E655A24C17AD9A9');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844C17AD9A9');
        $this->addSql('ALTER TABLE authorization DROP FOREIGN KEY FK_7A6D8BEFC17AD9A9');
        $this->addSql('ALTER TABLE professional_rate DROP FOREIGN KEY FK_CDA906E7C17AD9A9');
        $this->addSql('CREATE TABLE financier (id INT AUTO_INCREMENT NOT NULL, financier_type_id INT NOT NULL, code VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, short_name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, rut VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, address LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, discount_percentage NUMERIC(5, 2) DEFAULT NULL, payment_days INT DEFAULT NULL, requires_authorization TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F0E352CBFA66F760 (financier_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE financier_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, is_agreement TINYINT(1) DEFAULT 0 NOT NULL, is_default TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE financier ADD CONSTRAINT FK_F0E352CBFA66F760 FOREIGN KEY (financier_type_id) REFERENCES financier_type (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE branch_payer DROP FOREIGN KEY FK_2F7A59CEDCD6CC49');
        $this->addSql('ALTER TABLE branch_payer DROP FOREIGN KEY FK_2F7A59CEC17AD9A9');
        $this->addSql('ALTER TABLE branch_service_type DROP FOREIGN KEY FK_1F793463DCD6CC49');
        $this->addSql('ALTER TABLE branch_service_type DROP FOREIGN KEY FK_1F793463AC8DE0F');
        $this->addSql('ALTER TABLE exam_package DROP FOREIGN KEY FK_D3CE3EFDC61D802A');
        $this->addSql('ALTER TABLE exam_package_detail DROP FOREIGN KEY FK_8E28E519E644D1AA');
        $this->addSql('ALTER TABLE exam_package_detail DROP FOREIGN KEY FK_8E28E519C61D802A');
        $this->addSql('ALTER TABLE medical_service_bed_type DROP FOREIGN KEY FK_EEE3D850C61D802A');
        $this->addSql('ALTER TABLE medical_service_bed_type DROP FOREIGN KEY FK_EEE3D8508158330E');
        $this->addSql('ALTER TABLE medical_service_budget_item DROP FOREIGN KEY FK_9E9803FBC61D802A');
        $this->addSql('ALTER TABLE medical_service_budget_item DROP FOREIGN KEY FK_9E9803FBA4665449');
        $this->addSql('ALTER TABLE medical_service_service DROP FOREIGN KEY FK_D8EE37BEC61D802A');
        $this->addSql('ALTER TABLE medical_service_service DROP FOREIGN KEY FK_D8EE37BEED5CA9E6');
        $this->addSql('ALTER TABLE payer DROP FOREIGN KEY FK_41CB5B99BEA5E0F7');
        $this->addSql('ALTER TABLE service_package_detail DROP FOREIGN KEY FK_139B51F9621D924B');
        $this->addSql('ALTER TABLE service_package_detail DROP FOREIGN KEY FK_139B51F9C61D802A');
        $this->addSql('ALTER TABLE specialty_branch DROP FOREIGN KEY FK_89FD01D99A353316');
        $this->addSql('ALTER TABLE specialty_branch DROP FOREIGN KEY FK_89FD01D9DCD6CC49');
        $this->addSql('ALTER TABLE warehouse DROP FOREIGN KEY FK_ECB38BFCED5CA9E6');
        $this->addSql('ALTER TABLE warehouse DROP FOREIGN KEY FK_ECB38BFCEEB1F0');
        $this->addSql('ALTER TABLE warehouse_medical_service DROP FOREIGN KEY FK_E698EAF5080ECDE');
        $this->addSql('ALTER TABLE warehouse_medical_service DROP FOREIGN KEY FK_E698EAFC61D802A');
        $this->addSql('DROP TABLE branch_payer');
        $this->addSql('DROP TABLE branch_service_type');
        $this->addSql('DROP TABLE exam_package');
        $this->addSql('DROP TABLE exam_package_detail');
        $this->addSql('DROP TABLE figure');
        $this->addSql('DROP TABLE medical_service_bed_type');
        $this->addSql('DROP TABLE medical_service_budget_item');
        $this->addSql('DROP TABLE medical_service_service');
        $this->addSql('DROP TABLE payer');
        $this->addSql('DROP TABLE payer_type');
        $this->addSql('DROP TABLE service_package');
        $this->addSql('DROP TABLE service_package_detail');
        $this->addSql('DROP TABLE specialty_branch');
        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP TABLE warehouse_medical_service');
        $this->addSql('DROP INDEX IDX_2E655A24C17AD9A9 ON agreement');
        $this->addSql('ALTER TABLE agreement CHANGE payer_id financier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agreement ADD CONSTRAINT FK_2E655A244A353075 FOREIGN KEY (financier_id) REFERENCES financier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2E655A244A353075 ON agreement (financier_id)');
        $this->addSql('DROP INDEX IDX_FE38F844C17AD9A9 ON appointment');
        $this->addSql('ALTER TABLE appointment CHANGE payer_id financier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8444A353075 FOREIGN KEY (financier_id) REFERENCES financier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_FE38F8444A353075 ON appointment (financier_id)');
        $this->addSql('DROP INDEX IDX_7A6D8BEFC17AD9A9 ON authorization');
        $this->addSql('ALTER TABLE authorization CHANGE payer_id financier_id INT NOT NULL');
        $this->addSql('ALTER TABLE authorization ADD CONSTRAINT FK_7A6D8BEF4A353075 FOREIGN KEY (financier_id) REFERENCES financier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7A6D8BEF4A353075 ON authorization (financier_id)');
        $this->addSql('ALTER TABLE medical_service DROP FOREIGN KEY FK_A79F7A1CD0EC18BF');
        $this->addSql('ALTER TABLE medical_service DROP FOREIGN KEY FK_A79F7A1CAC8DE0F');
        $this->addSql('ALTER TABLE medical_service DROP FOREIGN KEY FK_A79F7A1CBB50891B');
        $this->addSql('ALTER TABLE medical_service DROP FOREIGN KEY FK_A79F7A1C3395B1AC');
        $this->addSql('DROP INDEX IDX_A79F7A1C5C011B5 ON medical_service');
        $this->addSql('DROP INDEX IDX_A79F7A1CD0EC18BF ON medical_service');
        $this->addSql('DROP INDEX IDX_A79F7A1CAC8DE0F ON medical_service');
        $this->addSql('DROP INDEX IDX_A79F7A1CBB50891B ON medical_service');
        $this->addSql('DROP INDEX IDX_A79F7A1C3395B1AC ON medical_service');
        $this->addSql('ALTER TABLE medical_service ADD department_id INT DEFAULT NULL, ADD hl7_service_type VARCHAR(50) DEFAULT NULL, DROP figure_id, DROP budget_item_id, DROP service_type_id, DROP sub_company_id, DROP billing_sub_company_id, DROP fonasa_code, DROP short_name, DROP accounting_code_erp, DROP duration, DROP applies_surcharge, DROP is_imed, DROP imed_code, DROP interface_code, DROP participation, DROP is_taxed, DROP is_procedure, CHANGE code code VARCHAR(100) DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE medical_service ADD CONSTRAINT FK_A79F7A1CAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_A79F7A1CAE80F5DF ON medical_service (department_id)');
        $this->addSql('DROP INDEX IDX_CDA906E7C17AD9A9 ON professional_rate');
        $this->addSql('ALTER TABLE professional_rate CHANGE payer_id financier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE professional_rate ADD CONSTRAINT FK_CDA906E74A353075 FOREIGN KEY (financier_id) REFERENCES financier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CDA906E74A353075 ON professional_rate (financier_id)');
    }
}
