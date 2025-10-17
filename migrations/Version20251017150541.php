<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Crear tabla Estado y agregar relaciones a todas las entidades
 */
final class Version20251017150541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear tabla Estado y agregar foreign keys a todas las entidades del sistema';
    }

    public function up(Schema $schema): void
    {
        // Crear tabla Estado
        $this->addSql('CREATE TABLE IF NOT EXISTS estado (
            id INT AUTO_INCREMENT NOT NULL,
            nombre_estado VARCHAR(45) NOT NULL,
            activo TINYINT(1) DEFAULT 1 NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_estado_activo (activo)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = "Estados/Status del sistema"');

        // Agregar columnas id_estado a todas las tablas existentes
        $this->addSql('ALTER TABLE pais ADD COLUMN id_estado INT DEFAULT NULL');
        $this->addSql('ALTER TABLE region ADD COLUMN id_estado INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Religion ADD COLUMN id_estado INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Sexo ADD COLUMN id_estado INT DEFAULT NULL');

        // Crear índices para las foreign keys
        $this->addSql('CREATE INDEX IDX_pais_estado ON pais (id_estado)');
        $this->addSql('CREATE INDEX IDX_region_estado ON region (id_estado)');
        $this->addSql('CREATE INDEX IDX_religion_estado ON Religion (id_estado)');
        $this->addSql('CREATE INDEX IDX_sexo_estado ON Sexo (id_estado)');

        // Crear foreign keys
        $this->addSql('ALTER TABLE pais ADD CONSTRAINT FK_pais_estado FOREIGN KEY (id_estado) REFERENCES estado (id)');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_region_estado FOREIGN KEY (id_estado) REFERENCES estado (id)');
        $this->addSql('ALTER TABLE Religion ADD CONSTRAINT FK_religion_estado FOREIGN KEY (id_estado) REFERENCES estado (id)');
        $this->addSql('ALTER TABLE Sexo ADD CONSTRAINT FK_sexo_estado FOREIGN KEY (id_estado) REFERENCES estado (id)');
    }

    public function down(Schema $schema): void
    {
        // Eliminar foreign keys
        $this->addSql('ALTER TABLE pais DROP FOREIGN KEY FK_pais_estado');
        $this->addSql('ALTER TABLE region DROP FOREIGN KEY FK_region_estado');
        $this->addSql('ALTER TABLE Religion DROP FOREIGN KEY FK_religion_estado');
        $this->addSql('ALTER TABLE Sexo DROP FOREIGN KEY FK_sexo_estado');

        // Eliminar índices
        $this->addSql('DROP INDEX IDX_pais_estado ON pais');
        $this->addSql('DROP INDEX IDX_region_estado ON region');
        $this->addSql('DROP INDEX IDX_religion_estado ON Religion');
        $this->addSql('DROP INDEX IDX_sexo_estado ON Sexo');

        // Eliminar columnas id_estado
        $this->addSql('ALTER TABLE pais DROP COLUMN id_estado');
        $this->addSql('ALTER TABLE region DROP COLUMN id_estado');
        $this->addSql('ALTER TABLE Religion DROP COLUMN id_estado');
        $this->addSql('ALTER TABLE Sexo DROP COLUMN id_estado');

        // Eliminar tabla estado
        $this->addSql('DROP TABLE IF EXISTS estado');
    }
}
