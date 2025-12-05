<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251205193413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `business_activity` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `country` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, nationality_name VARCHAR(100) NOT NULL, country_code_hl7 VARCHAR(5) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `education_level` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_default_value TINYINT(1) DEFAULT 1 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `ethnic_group` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `health_insurance` (id INT AUTO_INCREMENT NOT NULL, health_insurance_type_id INT DEFAULT NULL, tax_affectation_type_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, interface_code VARCHAR(100) DEFAULT NULL, id_imed INT DEFAULT NULL, abbreviated_name VARCHAR(45) NOT NULL, is_default_value TINYINT(1) DEFAULT 1 NOT NULL, healthcare_prevition_hl7 VARCHAR(30) DEFAULT NULL, icon VARCHAR(45) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_558804DE54CCFBDD (health_insurance_type_id), INDEX IDX_558804DE588DA015 (tax_affectation_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE health_insurance_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_default_value TINYINT(1) DEFAULT 1 NOT NULL, is_agreement TINYINT(1) DEFAULT 1 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `identification_type` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `marial_status` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_default_value TINYINT(1) DEFAULT 1 NOT NULL, marital_status_code_hl7 VARCHAR(2) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `municipality` (id INT AUTO_INCREMENT NOT NULL, province_id INT NOT NULL, name VARCHAR(100) NOT NULL, municipality_code_hl7 VARCHAR(10) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_C6F56628E946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `occupation` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_default_value TINYINT(1) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pais (id INT AUTO_INCREMENT NOT NULL, nombre_pais VARCHAR(255) DEFAULT NULL, nombre_gentilicio VARCHAR(255) NOT NULL, activo TINYINT(1) DEFAULT 1 NOT NULL, id_estado TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `person` (id INT AUTO_INCREMENT NOT NULL, identification_type_id INT DEFAULT NULL, education_level_id INT DEFAULT NULL, marital_status_id INT DEFAULT NULL, occupation_id INT DEFAULT NULL, ethnic_group_id INT DEFAULT NULL, religion_id INT DEFAULT NULL, gender_id INT DEFAULT NULL, nacionality_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', identification VARCHAR(100) NOT NULL, name VARCHAR(60) NOT NULL, last_name VARCHAR(45) NOT NULL, middle_name VARCHAR(45) DEFAULT NULL, birth_date_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', death_date_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', non_medical_note LONGTEXT, photo_path VARCHAR(255) DEFAULT NULL, is_record_visibility TINYINT(1) DEFAULT 1 NOT NULL, record_viewed_date_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', social_name VARCHAR(50) DEFAULT NULL, number_of_children INT DEFAULT NULL, mobile_phone VARCHAR(20) NOT NULL, work_phone VARCHAR(20) DEFAULT NULL, home_phone VARCHAR(20) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, secondary_email VARCHAR(100) DEFAULT NULL, contact_method VARCHAR(50) DEFAULT NULL, twin_number INT DEFAULT NULL, INDEX IDX_34DCD176F54A83F (identification_type_id), INDEX IDX_34DCD176D7A5352E (education_level_id), INDEX IDX_34DCD176E559F9BF (marital_status_id), INDEX IDX_34DCD17622C8FC20 (occupation_id), INDEX IDX_34DCD176E7EEEDB3 (ethnic_group_id), INDEX IDX_34DCD176B7850CBD (religion_id), INDEX IDX_34DCD176708A0E0 (gender_id), INDEX IDX_34DCD1767949DE26 (nacionality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_address (id INT AUTO_INCREMENT NOT NULL, id_person_id INT DEFAULT NULL, municipality_id INT DEFAULT NULL, country_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', address VARCHAR(255) NOT NULL, street_number VARCHAR(10) NOT NULL, address_details VARCHAR(255) NOT NULL, INDEX IDX_2FD0DC08A14E0760 (id_person_id), INDEX IDX_2FD0DC08AE6F181C (municipality_id), INDEX IDX_2FD0DC08F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `province` (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, name VARCHAR(100) NOT NULL, province_code_hl7 VARCHAR(10) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_4ADAD40B98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `region` (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, address_state_hl7 VARCHAR(10) DEFAULT NULL, region_code_hl7 VARCHAR(10) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_F62F176F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `religion` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_default_value TINYINT(1) DEFAULT 1 NOT NULL, religion_code_hl7 VARCHAR(10) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sexo (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, codigo VARCHAR(10) NOT NULL, activo TINYINT(1) DEFAULT 1 NOT NULL, id_estado TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `tax_affectation_type` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `health_insurance` ADD CONSTRAINT FK_558804DE54CCFBDD FOREIGN KEY (health_insurance_type_id) REFERENCES health_insurance_type (id)');
        $this->addSql('ALTER TABLE `health_insurance` ADD CONSTRAINT FK_558804DE588DA015 FOREIGN KEY (tax_affectation_type_id) REFERENCES `tax_affectation_type` (id)');
        $this->addSql('ALTER TABLE `municipality` ADD CONSTRAINT FK_C6F56628E946114A FOREIGN KEY (province_id) REFERENCES `province` (id)');
        $this->addSql('ALTER TABLE `person` ADD CONSTRAINT FK_34DCD176F54A83F FOREIGN KEY (identification_type_id) REFERENCES `identification_type` (id)');
        $this->addSql('ALTER TABLE `person` ADD CONSTRAINT FK_34DCD176D7A5352E FOREIGN KEY (education_level_id) REFERENCES `education_level` (id)');
        $this->addSql('ALTER TABLE `person` ADD CONSTRAINT FK_34DCD176E559F9BF FOREIGN KEY (marital_status_id) REFERENCES `marial_status` (id)');
        $this->addSql('ALTER TABLE `person` ADD CONSTRAINT FK_34DCD17622C8FC20 FOREIGN KEY (occupation_id) REFERENCES `occupation` (id)');
        $this->addSql('ALTER TABLE `person` ADD CONSTRAINT FK_34DCD176E7EEEDB3 FOREIGN KEY (ethnic_group_id) REFERENCES `ethnic_group` (id)');
        $this->addSql('ALTER TABLE `person` ADD CONSTRAINT FK_34DCD176B7850CBD FOREIGN KEY (religion_id) REFERENCES `religion` (id)');
        $this->addSql('ALTER TABLE `person` ADD CONSTRAINT FK_34DCD176708A0E0 FOREIGN KEY (gender_id) REFERENCES gender (id)');
        $this->addSql('ALTER TABLE `person` ADD CONSTRAINT FK_34DCD1767949DE26 FOREIGN KEY (nacionality_id) REFERENCES `country` (id)');
        $this->addSql('ALTER TABLE person_address ADD CONSTRAINT FK_2FD0DC08A14E0760 FOREIGN KEY (id_person_id) REFERENCES `person` (id)');
        $this->addSql('ALTER TABLE person_address ADD CONSTRAINT FK_2FD0DC08AE6F181C FOREIGN KEY (municipality_id) REFERENCES `municipality` (id)');
        $this->addSql('ALTER TABLE person_address ADD CONSTRAINT FK_2FD0DC08F92F3E70 FOREIGN KEY (country_id) REFERENCES `country` (id)');
        $this->addSql('ALTER TABLE `province` ADD CONSTRAINT FK_4ADAD40B98260155 FOREIGN KEY (region_id) REFERENCES `region` (id)');
        $this->addSql('ALTER TABLE `region` ADD CONSTRAINT FK_F62F176F92F3E70 FOREIGN KEY (country_id) REFERENCES `country` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `health_insurance` DROP FOREIGN KEY FK_558804DE54CCFBDD');
        $this->addSql('ALTER TABLE `health_insurance` DROP FOREIGN KEY FK_558804DE588DA015');
        $this->addSql('ALTER TABLE `municipality` DROP FOREIGN KEY FK_C6F56628E946114A');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD176F54A83F');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD176D7A5352E');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD176E559F9BF');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD17622C8FC20');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD176E7EEEDB3');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD176B7850CBD');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD176708A0E0');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD1767949DE26');
        $this->addSql('ALTER TABLE person_address DROP FOREIGN KEY FK_2FD0DC08A14E0760');
        $this->addSql('ALTER TABLE person_address DROP FOREIGN KEY FK_2FD0DC08AE6F181C');
        $this->addSql('ALTER TABLE person_address DROP FOREIGN KEY FK_2FD0DC08F92F3E70');
        $this->addSql('ALTER TABLE `province` DROP FOREIGN KEY FK_4ADAD40B98260155');
        $this->addSql('ALTER TABLE `region` DROP FOREIGN KEY FK_F62F176F92F3E70');
        $this->addSql('DROP TABLE `business_activity`');
        $this->addSql('DROP TABLE `country`');
        $this->addSql('DROP TABLE `education_level`');
        $this->addSql('DROP TABLE `ethnic_group`');
        $this->addSql('DROP TABLE `health_insurance`');
        $this->addSql('DROP TABLE health_insurance_type');
        $this->addSql('DROP TABLE `identification_type`');
        $this->addSql('DROP TABLE `marial_status`');
        $this->addSql('DROP TABLE `municipality`');
        $this->addSql('DROP TABLE `occupation`');
        $this->addSql('DROP TABLE pais');
        $this->addSql('DROP TABLE `person`');
        $this->addSql('DROP TABLE person_address');
        $this->addSql('DROP TABLE `province`');
        $this->addSql('DROP TABLE `region`');
        $this->addSql('DROP TABLE `religion`');
        $this->addSql('DROP TABLE sexo');
        $this->addSql('DROP TABLE `tax_affectation_type`');
    }
}
