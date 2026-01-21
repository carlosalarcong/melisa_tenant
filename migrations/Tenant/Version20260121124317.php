<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260121124317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Agregar tablas para sistema de permisos por tenant: tenant_permission_profile y tenant_module_permission_override';
    }

    public function up(Schema $schema): void
    {
        // Crear tabla para perfil de permisos del tenant
        $this->addSql('CREATE TABLE tenant_permission_profile (
            id INT AUTO_INCREMENT NOT NULL, 
            profile_type VARCHAR(50) NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Crear tabla para overrides de permisos por módulo
        $this->addSql('CREATE TABLE tenant_module_permission_override (
            id INT AUTO_INCREMENT NOT NULL, 
            module_name VARCHAR(100) NOT NULL, 
            required_roles JSON NOT NULL, 
            description VARCHAR(255) DEFAULT NULL, 
            is_active TINYINT(1) NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            INDEX idx_module_name (module_name), 
            UNIQUE INDEX unique_module_permission (module_name), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // Rollback: eliminar las tablas de permisos
        $this->addSql('DROP TABLE IF EXISTS tenant_module_permission_override');
        $this->addSql('DROP TABLE IF EXISTS tenant_permission_profile');
    }
}
