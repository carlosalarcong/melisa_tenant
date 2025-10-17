<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Crear entidades Sexo y Religion para sistema multi-tenant
 */
final class Version20251017145349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear tablas Sexo y Religion para catálogos del sistema';
    }

    public function up(Schema $schema): void
    {
        // Crear tabla Sexo
        $this->addSql('CREATE TABLE IF NOT EXISTS Sexo (
            id INT AUTO_INCREMENT NOT NULL,
            nombre VARCHAR(50) NOT NULL,
            codigo VARCHAR(10) NOT NULL,
            activo TINYINT(1) DEFAULT 1 NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_sexo_activo (activo),
            INDEX idx_sexo_codigo (codigo)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = "Catálogo de géneros/sexos"');

        // Crear tabla Religion
        $this->addSql('CREATE TABLE IF NOT EXISTS Religion (
            id INT AUTO_INCREMENT NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            codigo VARCHAR(20) NOT NULL,
            descripcion TEXT DEFAULT NULL,
            activo TINYINT(1) DEFAULT 1 NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_religion_activo (activo),
            INDEX idx_religion_codigo (codigo)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = "Catálogo de religiones"');
    }

    public function down(Schema $schema): void
    {
        // Eliminar las tablas en orden inverso
        $this->addSql('DROP TABLE IF EXISTS Religion');
        $this->addSql('DROP TABLE IF EXISTS Sexo');
    }
}
