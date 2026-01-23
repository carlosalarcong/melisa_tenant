<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260121182820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE maintainer_doctor_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(20) DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintainer_education_level_detail (id INT AUTO_INCREMENT NOT NULL, education_level_id INT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(20) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_D867A8A7D7A5352E (education_level_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintainer_insurance_administrator (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(20) DEFAULT NULL, rut VARCHAR(50) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintainer_job_position (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(20) DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintainer_location (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(20) DEFAULT NULL, description LONGTEXT DEFAULT NULL, floor VARCHAR(50) DEFAULT NULL, building VARCHAR(50) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintainer_medical_box (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(20) DEFAULT NULL, description LONGTEXT DEFAULT NULL, capacity INT DEFAULT NULL, floor VARCHAR(50) DEFAULT NULL, building VARCHAR(50) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintainer_origin (id INT AUTO_INCREMENT NOT NULL, origin_type_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(20) DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_23B5077C703B99FE (origin_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintainer_origin_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(20) DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE maintainer_education_level_detail ADD CONSTRAINT FK_D867A8A7D7A5352E FOREIGN KEY (education_level_id) REFERENCES `education_level` (id)');
        $this->addSql('ALTER TABLE maintainer_origin ADD CONSTRAINT FK_23B5077C703B99FE FOREIGN KEY (origin_type_id) REFERENCES maintainer_origin_type (id)');
        $this->addSql('ALTER TABLE education_level ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE ethnic_group ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE marital_status ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE occupation ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE religion ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE sexo CHANGE code code VARCHAR(10) DEFAULT NULL, CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE id_estado id_estado INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE maintainer_education_level_detail DROP FOREIGN KEY FK_D867A8A7D7A5352E');
        $this->addSql('ALTER TABLE maintainer_origin DROP FOREIGN KEY FK_23B5077C703B99FE');
        $this->addSql('DROP TABLE maintainer_doctor_type');
        $this->addSql('DROP TABLE maintainer_education_level_detail');
        $this->addSql('DROP TABLE maintainer_insurance_administrator');
        $this->addSql('DROP TABLE maintainer_job_position');
        $this->addSql('DROP TABLE maintainer_location');
        $this->addSql('DROP TABLE maintainer_medical_box');
        $this->addSql('DROP TABLE maintainer_origin');
        $this->addSql('DROP TABLE maintainer_origin_type');
        $this->addSql('ALTER TABLE `education_level` DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE `ethnic_group` DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE `marital_status` DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE `occupation` DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE `religion` DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE sexo CHANGE code code VARCHAR(10) NOT NULL, CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL, CHANGE id_estado id_estado TINYINT(1) DEFAULT 1 NOT NULL');
    }
}
