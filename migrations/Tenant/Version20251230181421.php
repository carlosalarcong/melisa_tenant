<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251230181421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE branch (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, address LONGTEXT DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, region VARCHAR(100) DEFAULT NULL, postal_code VARCHAR(20) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_BB861B1F32C8A3DE (organization_id), INDEX IDX_BB861B1F5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, branch_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_CD1DE18ADCD6CC49 (branch_id), INDEX IDX_CD1DE18A5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE license (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_5768F41932C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medical_service (id INT AUTO_INCREMENT NOT NULL, department_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, hl7_service_type VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A79F7A1CAE80F5DF (department_id), INDEX IDX_A79F7A1C5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medical_specialty (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, snomed_code VARCHAR(50) DEFAULT NULL, hl7_specialty_code VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1515F85632C8A3DE (organization_id), INDEX IDX_1515F8565D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_service (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, service_id INT NOT NULL, state_id INT NOT NULL, is_active TINYINT(1) DEFAULT 0 NOT NULL, assigned_at DATETIME DEFAULT NULL, activated_at DATETIME DEFAULT NULL, INDEX IDX_5E692D467597D3FE (member_id), INDEX IDX_5E692D46ED5CA9E6 (service_id), INDEX IDX_5E692D465D83CC1 (state_id), UNIQUE INDEX unique_member_service (member_id, service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_specialty (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, specialty_id INT NOT NULL, state_id INT NOT NULL, block_date DATETIME DEFAULT NULL, registration_number VARCHAR(100) DEFAULT NULL, assigned_at DATETIME DEFAULT NULL, certification_date DATETIME DEFAULT NULL, expiration_date DATETIME DEFAULT NULL, INDEX IDX_A04E70297597D3FE (member_id), INDEX IDX_A04E70299A353316 (specialty_id), INDEX IDX_A04E70295D83CC1 (state_id), UNIQUE INDEX unique_member_specialty (member_id, specialty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_history (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, password_hash VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_F3521447597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, snomed_code VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_462CE4F532C8A3DE (organization_id), INDEX IDX_462CE4F55D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professional_type (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9E9B6EF732C8A3DE (organization_id), INDEX IDX_9E9B6EF75D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, state_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, web_description LONGTEXT DEFAULT NULL, overbooking_limit INT DEFAULT NULL, is_clinical_professional TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_57698A6A5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE branch ADD CONSTRAINT FK_BB861B1F32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE branch ADD CONSTRAINT FK_BB861B1F5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18ADCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE license ADD CONSTRAINT FK_5768F41932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE medical_service ADD CONSTRAINT FK_A79F7A1CAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE medical_service ADD CONSTRAINT FK_A79F7A1C5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE medical_specialty ADD CONSTRAINT FK_1515F85632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE medical_specialty ADD CONSTRAINT FK_1515F8565D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE member_service ADD CONSTRAINT FK_5E692D467597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_service ADD CONSTRAINT FK_5E692D46ED5CA9E6 FOREIGN KEY (service_id) REFERENCES medical_service (id)');
        $this->addSql('ALTER TABLE member_service ADD CONSTRAINT FK_5E692D465D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE member_specialty ADD CONSTRAINT FK_A04E70297597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_specialty ADD CONSTRAINT FK_A04E70299A353316 FOREIGN KEY (specialty_id) REFERENCES medical_specialty (id)');
        $this->addSql('ALTER TABLE member_specialty ADD CONSTRAINT FK_A04E70295D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE password_history ADD CONSTRAINT FK_F3521447597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F532C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F55D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE professional_type ADD CONSTRAINT FK_9E9B6EF732C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE professional_type ADD CONSTRAINT FK_9E9B6EF75D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE branch DROP FOREIGN KEY FK_BB861B1F32C8A3DE');
        $this->addSql('ALTER TABLE branch DROP FOREIGN KEY FK_BB861B1F5D83CC1');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18ADCD6CC49');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A5D83CC1');
        $this->addSql('ALTER TABLE license DROP FOREIGN KEY FK_5768F41932C8A3DE');
        $this->addSql('ALTER TABLE medical_service DROP FOREIGN KEY FK_A79F7A1CAE80F5DF');
        $this->addSql('ALTER TABLE medical_service DROP FOREIGN KEY FK_A79F7A1C5D83CC1');
        $this->addSql('ALTER TABLE medical_specialty DROP FOREIGN KEY FK_1515F85632C8A3DE');
        $this->addSql('ALTER TABLE medical_specialty DROP FOREIGN KEY FK_1515F8565D83CC1');
        $this->addSql('ALTER TABLE member_service DROP FOREIGN KEY FK_5E692D467597D3FE');
        $this->addSql('ALTER TABLE member_service DROP FOREIGN KEY FK_5E692D46ED5CA9E6');
        $this->addSql('ALTER TABLE member_service DROP FOREIGN KEY FK_5E692D465D83CC1');
        $this->addSql('ALTER TABLE member_specialty DROP FOREIGN KEY FK_A04E70297597D3FE');
        $this->addSql('ALTER TABLE member_specialty DROP FOREIGN KEY FK_A04E70299A353316');
        $this->addSql('ALTER TABLE member_specialty DROP FOREIGN KEY FK_A04E70295D83CC1');
        $this->addSql('ALTER TABLE password_history DROP FOREIGN KEY FK_F3521447597D3FE');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F532C8A3DE');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F55D83CC1');
        $this->addSql('ALTER TABLE professional_type DROP FOREIGN KEY FK_9E9B6EF732C8A3DE');
        $this->addSql('ALTER TABLE professional_type DROP FOREIGN KEY FK_9E9B6EF75D83CC1');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6A5D83CC1');
        $this->addSql('DROP TABLE branch');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE license');
        $this->addSql('DROP TABLE medical_service');
        $this->addSql('DROP TABLE medical_specialty');
        $this->addSql('DROP TABLE member_service');
        $this->addSql('DROP TABLE member_specialty');
        $this->addSql('DROP TABLE password_history');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE professional_type');
        $this->addSql('DROP TABLE role');
    }
}
