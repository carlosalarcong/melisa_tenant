<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251027141045 extends AbstractMigration
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
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
