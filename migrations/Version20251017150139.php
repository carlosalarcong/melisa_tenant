<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Crear entidades Pais y Region con relaciones para sistema multi-tenant
 */
final class Version20251017150139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear tablas Pais y Region con relación uno a muchos';
    }

    public function up(Schema $schema): void
    {
        // Crear tabla Pais
        $this->addSql('CREATE TABLE IF NOT EXISTS pais (
            id INT AUTO_INCREMENT NOT NULL,
            nombre_pais VARCHAR(255) DEFAULT NULL,
            nombre_gentilicio VARCHAR(255) NOT NULL,
            activo TINYINT(1) DEFAULT 1 NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_pais_activo (activo)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = "Catálogo de países"');

        // Crear tabla Region
        $this->addSql('CREATE TABLE IF NOT EXISTS region (
            id INT AUTO_INCREMENT NOT NULL,
            id_pais INT DEFAULT NULL,
            codigo_region INT DEFAULT NULL,
            nombre_region VARCHAR(100) DEFAULT NULL,
            address_state_hl7 VARCHAR(10) DEFAULT NULL,
            activo TINYINT(1) DEFAULT 1 NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_region_activo (activo),
            INDEX idx_region_codigo (codigo_region),
            INDEX IDX_F62F176F85E0677 (id_pais),
            CONSTRAINT FK_F62F176F85E0677 FOREIGN KEY (id_pais) REFERENCES pais (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = "Catálogo de regiones por país"');
    }

    public function down(Schema $schema): void
    {
        // Eliminar las tablas en orden inverso (primero la que tiene la FK)
        $this->addSql('DROP TABLE IF EXISTS region');
        $this->addSql('DROP TABLE IF EXISTS pais');
    }
}
