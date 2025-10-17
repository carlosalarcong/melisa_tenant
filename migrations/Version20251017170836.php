<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251017170836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE estado (id INT AUTO_INCREMENT NOT NULL, nombre_estado VARCHAR(45) NOT NULL, activo TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `member` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pais (id INT AUTO_INCREMENT NOT NULL, id_estado INT DEFAULT NULL, nombre_pais VARCHAR(255) DEFAULT NULL, nombre_gentilicio VARCHAR(255) NOT NULL, activo TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_7E5D2EFF6A540E (id_estado), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, id_pais INT DEFAULT NULL, id_estado INT DEFAULT NULL, codigo_region INT DEFAULT NULL, nombre_region VARCHAR(100) DEFAULT NULL, address_state_hl7 VARCHAR(10) DEFAULT NULL, activo TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_F62F176F57D32FD (id_pais), INDEX IDX_F62F1766A540E (id_estado), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE religion (id INT AUTO_INCREMENT NOT NULL, id_estado INT DEFAULT NULL, nombre VARCHAR(100) NOT NULL, codigo VARCHAR(20) NOT NULL, descripcion LONGTEXT DEFAULT NULL, activo TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_1055F4F96A540E (id_estado), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sexo (id INT AUTO_INCREMENT NOT NULL, id_estado INT DEFAULT NULL, nombre VARCHAR(50) NOT NULL, codigo VARCHAR(10) NOT NULL, activo TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_2C3956926A540E (id_estado), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pais ADD CONSTRAINT FK_7E5D2EFF6A540E FOREIGN KEY (id_estado) REFERENCES estado (id)');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176F57D32FD FOREIGN KEY (id_pais) REFERENCES pais (id)');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F1766A540E FOREIGN KEY (id_estado) REFERENCES estado (id)');
        $this->addSql('ALTER TABLE religion ADD CONSTRAINT FK_1055F4F96A540E FOREIGN KEY (id_estado) REFERENCES estado (id)');
        $this->addSql('ALTER TABLE sexo ADD CONSTRAINT FK_2C3956926A540E FOREIGN KEY (id_estado) REFERENCES estado (id)');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP TABLE system_config');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'Nombre del centro médico\', subdomain VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'Subdominio para acceso\', domain VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'Dominio personalizado opcional\', database_name VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'Nombre de la base de datos del tenant\', rut_empresa VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'RUT de la empresa\', host VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'localhost\' COLLATE `utf8mb4_unicode_ci` COMMENT \'Host de la base de datos\', host_port INT DEFAULT 3306 COMMENT \'Puerto de la base de datos\', db_user VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'melisa\' COLLATE `utf8mb4_unicode_ci` COMMENT \'Usuario de la base de datos\', db_password VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'melisamelisa\' COLLATE `utf8mb4_unicode_ci` COMMENT \'Contraseña de la base de datos\', driver VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'mysql\' COLLATE `utf8mb4_unicode_ci` COMMENT \'Driver de base de datos\', version VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'latest\' COLLATE `utf8mb4_unicode_ci` COMMENT \'Versión de la base de datos\', language VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'es\' COLLATE `utf8mb4_unicode_ci` COMMENT \'Idioma del tenant\', is_active TINYINT(1) DEFAULT 1 NOT NULL COMMENT \'Estado activo del tenant\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_active (is_active), INDEX idx_domain (domain), INDEX idx_subdomain (subdomain), INDEX idx_tenant_active_subdomain (is_active, subdomain), INDEX idx_tenant_rut (rut_empresa), UNIQUE INDEX rut_empresa (rut_empresa), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'Tabla principal de tenants del sistema multi-tenant\' ');
        $this->addSql('CREATE TABLE system_config (id INT AUTO_INCREMENT NOT NULL, config_key VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, config_value TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, is_active TINYINT(1) DEFAULT 1, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX config_key (config_key), INDEX idx_config_active_key (is_active, config_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pais DROP FOREIGN KEY FK_7E5D2EFF6A540E');
        $this->addSql('ALTER TABLE region DROP FOREIGN KEY FK_F62F176F57D32FD');
        $this->addSql('ALTER TABLE region DROP FOREIGN KEY FK_F62F1766A540E');
        $this->addSql('ALTER TABLE religion DROP FOREIGN KEY FK_1055F4F96A540E');
        $this->addSql('ALTER TABLE sexo DROP FOREIGN KEY FK_2C3956926A540E');
        $this->addSql('DROP TABLE estado');
        $this->addSql('DROP TABLE `member`');
        $this->addSql('DROP TABLE pais');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE religion');
        $this->addSql('DROP TABLE sexo');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
