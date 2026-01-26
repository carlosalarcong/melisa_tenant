<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126155911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agreement (id INT AUTO_INCREMENT NOT NULL, financier_id INT DEFAULT NULL, requesting_company_id INT DEFAULT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(150) NOT NULL, agreement_type VARCHAR(100) DEFAULT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, discount_percentage NUMERIC(5, 2) DEFAULT NULL, payment_term_days INT DEFAULT NULL, terms LONGTEXT DEFAULT NULL, covered_services LONGTEXT DEFAULT NULL, status VARCHAR(50) NOT NULL, auto_renew TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2E655A244A353075 (financier_id), INDEX IDX_2E655A24E3FB3114 (requesting_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appointment (id INT AUTO_INCREMENT NOT NULL, schedule_id INT NOT NULL, professional_id INT NOT NULL, consultation_type_id INT DEFAULT NULL, financier_id INT DEFAULT NULL, authorization_id INT DEFAULT NULL, cancellation_type_id INT DEFAULT NULL, appointment_number VARCHAR(50) NOT NULL, patient_name VARCHAR(100) NOT NULL, patient_rut VARCHAR(20) NOT NULL, patient_email VARCHAR(100) DEFAULT NULL, patient_phone VARCHAR(50) DEFAULT NULL, appointment_date_time DATETIME NOT NULL, duration_minutes INT DEFAULT 30 NOT NULL, status VARCHAR(50) NOT NULL, cancellation_date DATETIME DEFAULT NULL, cancellation_reason LONGTEXT DEFAULT NULL, reason LONGTEXT DEFAULT NULL, observations LONGTEXT DEFAULT NULL, is_first_time TINYINT(1) DEFAULT 0 NOT NULL, reminder_sent TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_FE38F844D1CEA01A (appointment_number), INDEX IDX_FE38F844A40BC2D5 (schedule_id), INDEX IDX_FE38F844DB77003 (professional_id), INDEX IDX_FE38F844804F7D71 (consultation_type_id), INDEX IDX_FE38F8444A353075 (financier_id), INDEX IDX_FE38F8442F8B0EB2 (authorization_id), INDEX IDX_FE38F844B0AFEE5E (cancellation_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_package (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, package_type VARCHAR(100) DEFAULT NULL, package_price NUMERIC(10, 2) DEFAULT NULL, is_pre_assembled TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE authorization (id INT AUTO_INCREMENT NOT NULL, financier_id INT NOT NULL, service_type_id INT DEFAULT NULL, authorization_code VARCHAR(50) NOT NULL, patient_name VARCHAR(100) DEFAULT NULL, patient_rut VARCHAR(20) DEFAULT NULL, authorization_date DATE NOT NULL, expiration_date DATE DEFAULT NULL, authorized_services LONGTEXT DEFAULT NULL, authorized_amount NUMERIC(10, 2) DEFAULT NULL, status VARCHAR(50) NOT NULL, observations LONGTEXT DEFAULT NULL, authorized_by VARCHAR(100) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_7A6D8BEF4A353075 (financier_id), INDEX IDX_7A6D8BEFAC8DE0F (service_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bed (id INT AUTO_INCREMENT NOT NULL, bed_type_id INT NOT NULL, room_id INT DEFAULT NULL, bed_number VARCHAR(20) NOT NULL, floor VARCHAR(50) DEFAULT NULL, wing VARCHAR(100) DEFAULT NULL, status VARCHAR(50) NOT NULL, last_maintenance_date DATE DEFAULT NULL, observations LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E647FCFF8158330E (bed_type_id), INDEX IDX_E647FCFF54177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clinic (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(150) NOT NULL, short_name VARCHAR(50) DEFAULT NULL, rut VARCHAR(20) DEFAULT NULL, address LONGTEXT NOT NULL, city VARCHAR(100) DEFAULT NULL, region VARCHAR(100) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, director VARCHAR(100) DEFAULT NULL, total_beds INT DEFAULT NULL, has_emergency TINYINT(1) DEFAULT 0 NOT NULL, has_icu TINYINT(1) DEFAULT 0 NOT NULL, is_main_facility TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pathology_article (id INT AUTO_INCREMENT NOT NULL, ges_pathology_id INT NOT NULL, article_name VARCHAR(200) NOT NULL, article_code VARCHAR(20) DEFAULT NULL, description LONGTEXT DEFAULT NULL, quantity INT DEFAULT 1 NOT NULL, unit_of_measure VARCHAR(50) DEFAULT NULL, unit_cost NUMERIC(10, 2) DEFAULT NULL, is_mandatory TINYINT(1) DEFAULT 1 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B24A1BE1541D0E27 (ges_pathology_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professional (id INT AUTO_INCREMENT NOT NULL, specialty_id INT DEFAULT NULL, code VARCHAR(20) DEFAULT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, rut VARCHAR(20) NOT NULL, professional_type VARCHAR(50) DEFAULT NULL, license_number VARCHAR(50) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, mobile VARCHAR(50) DEFAULT NULL, address LONGTEXT DEFAULT NULL, hire_date DATE DEFAULT NULL, is_external TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B3B573AA9A353316 (specialty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professional_rate (id INT AUTO_INCREMENT NOT NULL, professional_id INT NOT NULL, service_id INT DEFAULT NULL, financier_id INT DEFAULT NULL, rate NUMERIC(10, 2) NOT NULL, effective_date DATE NOT NULL, expiration_date DATE DEFAULT NULL, observations LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_CDA906E7DB77003 (professional_id), INDEX IDX_CDA906E7ED5CA9E6 (service_id), INDEX IDX_CDA906E74A353075 (financier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, clinic_id INT DEFAULT NULL, room_number VARCHAR(50) NOT NULL, name VARCHAR(150) NOT NULL, room_type VARCHAR(100) DEFAULT NULL, floor VARCHAR(50) DEFAULT NULL, wing VARCHAR(100) DEFAULT NULL, capacity INT DEFAULT 1 NOT NULL, daily_rate NUMERIC(10, 2) DEFAULT NULL, status VARCHAR(50) NOT NULL, has_oxygen TINYINT(1) DEFAULT 0 NOT NULL, has_bathroom TINYINT(1) DEFAULT 0 NOT NULL, observations LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_729F519BCC22AD4 (clinic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, professional_id INT NOT NULL, consultation_type_id INT DEFAULT NULL, blocking_type_id INT DEFAULT NULL, schedule_date DATE NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, slot_duration_minutes INT DEFAULT 30 NOT NULL, location VARCHAR(100) DEFAULT NULL, max_appointments INT DEFAULT 1 NOT NULL, is_blocked TINYINT(1) DEFAULT 0 NOT NULL, observations LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5A3811FBDB77003 (professional_id), INDEX IDX_5A3811FB804F7D71 (consultation_type_id), INDEX IDX_5A3811FB6DB2886B (blocking_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, service_type_id INT DEFAULT NULL, specialty_id INT DEFAULT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, base_price NUMERIC(10, 2) DEFAULT NULL, estimated_duration_minutes INT DEFAULT NULL, requires_authorization TINYINT(1) DEFAULT 0 NOT NULL, requires_specialist TINYINT(1) DEFAULT 0 NOT NULL, is_ambulatory TINYINT(1) DEFAULT 0 NOT NULL, fonasa_code VARCHAR(50) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E19D9AD2AC8DE0F (service_type_id), INDEX IDX_E19D9AD29A353316 (specialty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE surgery_item (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) DEFAULT NULL, name VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, category VARCHAR(100) DEFAULT NULL, unit_of_measure VARCHAR(50) DEFAULT NULL, unit_cost NUMERIC(10, 2) DEFAULT NULL, is_sterile TINYINT(1) DEFAULT 0 NOT NULL, is_disposable TINYINT(1) DEFAULT 0 NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agreement ADD CONSTRAINT FK_2E655A244A353075 FOREIGN KEY (financier_id) REFERENCES financier (id)');
        $this->addSql('ALTER TABLE agreement ADD CONSTRAINT FK_2E655A24E3FB3114 FOREIGN KEY (requesting_company_id) REFERENCES requesting_company (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844DB77003 FOREIGN KEY (professional_id) REFERENCES professional (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844804F7D71 FOREIGN KEY (consultation_type_id) REFERENCES consultation_type (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8444A353075 FOREIGN KEY (financier_id) REFERENCES financier (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8442F8B0EB2 FOREIGN KEY (authorization_id) REFERENCES authorization (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844B0AFEE5E FOREIGN KEY (cancellation_type_id) REFERENCES cancellation_type (id)');
        $this->addSql('ALTER TABLE authorization ADD CONSTRAINT FK_7A6D8BEF4A353075 FOREIGN KEY (financier_id) REFERENCES financier (id)');
        $this->addSql('ALTER TABLE authorization ADD CONSTRAINT FK_7A6D8BEFAC8DE0F FOREIGN KEY (service_type_id) REFERENCES service_type (id)');
        $this->addSql('ALTER TABLE bed ADD CONSTRAINT FK_E647FCFF8158330E FOREIGN KEY (bed_type_id) REFERENCES bed_type (id)');
        $this->addSql('ALTER TABLE bed ADD CONSTRAINT FK_E647FCFF54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE pathology_article ADD CONSTRAINT FK_B24A1BE1541D0E27 FOREIGN KEY (ges_pathology_id) REFERENCES ges_pathology (id)');
        $this->addSql('ALTER TABLE professional ADD CONSTRAINT FK_B3B573AA9A353316 FOREIGN KEY (specialty_id) REFERENCES specialty (id)');
        $this->addSql('ALTER TABLE professional_rate ADD CONSTRAINT FK_CDA906E7DB77003 FOREIGN KEY (professional_id) REFERENCES professional (id)');
        $this->addSql('ALTER TABLE professional_rate ADD CONSTRAINT FK_CDA906E7ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE professional_rate ADD CONSTRAINT FK_CDA906E74A353075 FOREIGN KEY (financier_id) REFERENCES financier (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BCC22AD4 FOREIGN KEY (clinic_id) REFERENCES clinic (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBDB77003 FOREIGN KEY (professional_id) REFERENCES professional (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB804F7D71 FOREIGN KEY (consultation_type_id) REFERENCES consultation_type (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB6DB2886B FOREIGN KEY (blocking_type_id) REFERENCES blocking_type (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2AC8DE0F FOREIGN KEY (service_type_id) REFERENCES service_type (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD29A353316 FOREIGN KEY (specialty_id) REFERENCES specialty (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agreement DROP FOREIGN KEY FK_2E655A244A353075');
        $this->addSql('ALTER TABLE agreement DROP FOREIGN KEY FK_2E655A24E3FB3114');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844A40BC2D5');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844DB77003');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844804F7D71');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8444A353075');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8442F8B0EB2');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844B0AFEE5E');
        $this->addSql('ALTER TABLE authorization DROP FOREIGN KEY FK_7A6D8BEF4A353075');
        $this->addSql('ALTER TABLE authorization DROP FOREIGN KEY FK_7A6D8BEFAC8DE0F');
        $this->addSql('ALTER TABLE bed DROP FOREIGN KEY FK_E647FCFF8158330E');
        $this->addSql('ALTER TABLE bed DROP FOREIGN KEY FK_E647FCFF54177093');
        $this->addSql('ALTER TABLE pathology_article DROP FOREIGN KEY FK_B24A1BE1541D0E27');
        $this->addSql('ALTER TABLE professional DROP FOREIGN KEY FK_B3B573AA9A353316');
        $this->addSql('ALTER TABLE professional_rate DROP FOREIGN KEY FK_CDA906E7DB77003');
        $this->addSql('ALTER TABLE professional_rate DROP FOREIGN KEY FK_CDA906E7ED5CA9E6');
        $this->addSql('ALTER TABLE professional_rate DROP FOREIGN KEY FK_CDA906E74A353075');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BCC22AD4');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FBDB77003');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB804F7D71');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB6DB2886B');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2AC8DE0F');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD29A353316');
        $this->addSql('DROP TABLE agreement');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE article_package');
        $this->addSql('DROP TABLE authorization');
        $this->addSql('DROP TABLE bed');
        $this->addSql('DROP TABLE clinic');
        $this->addSql('DROP TABLE pathology_article');
        $this->addSql('DROP TABLE professional');
        $this->addSql('DROP TABLE professional_rate');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE surgery_item');
    }
}
