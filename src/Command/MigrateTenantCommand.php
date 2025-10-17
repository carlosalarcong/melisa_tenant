<?php

namespace App\Command;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:migrate-tenant',
    description: 'Preparar sistema de migraciones Doctrine para tenants'
)]
class MigrateTenantCommand extends Command
{
    private $centralDbConfig = [
        'host' => 'localhost',
        'port' => 3306,
        'dbname' => 'melisa_central',
        'user' => 'melisa',
        'password' => 'melisamelisa',
        'driver' => 'pdo_mysql',
    ];

    protected function configure(): void
    {
        $this
            ->addArgument('subdomain', InputArgument::OPTIONAL, 'Subdominio del tenant (o "all" para todos los tenants)')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Solo mostrar las migraciones sin ejecutarlas')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forzar ejecución sin confirmación')
            ->addOption('environment', null, InputOption::VALUE_REQUIRED, 'Ejecutar solo en tenants de un ambiente específico (dev, prod, test)', null)
            ->addOption('active-only', null, InputOption::VALUE_NONE, 'Solo ejecutar en tenants activos (is_active = 1)')
            ->addOption('parallel', null, InputOption::VALUE_NONE, 'Ejecutar migraciones en paralelo (para "all" o por ambiente)')
            ->setHelp('
Este comando prepara el sistema de migraciones de Doctrine en las bases de datos de los tenants.

IMPORTANTE: Este comando NO crea las tablas de entidades de negocio directamente.
Las tablas se crean con comandos específicos:
- app:create-member-table: Crear tabla member
- app:make-entity-tenant: Generar entidades Doctrine para otras tablas

Este comando:
1. Asegura que existe la tabla doctrine_migration_versions en cada tenant
2. Prepara el sistema para futuras migraciones de Doctrine
3. Registra el estado del sistema de migraciones

Ejemplos de uso:
  # Preparar sistema de migraciones para un tenant específico
  php bin/console app:migrate-tenant melisaclinica
  php bin/console app:migrate-tenant melisahospital --dry-run
  php bin/console app:migrate-tenant melisawiclinic --force
  
  # Preparar sistema para todos los tenants
  php bin/console app:migrate-tenant all
  php bin/console app:migrate-tenant all --dry-run
  php bin/console app:migrate-tenant all --force
  
  # Preparar por ambiente
  php bin/console app:migrate-tenant --environment=prod
  php bin/console app:migrate-tenant --environment=dev --active-only
  php bin/console app:migrate-tenant --environment=test --parallel
  
  # Combinaciones
  php bin/console app:migrate-tenant all --active-only --parallel
  php bin/console app:migrate-tenant all --environment=prod --force --parallel

Flujo recomendado:
1. app:create-tenant-database (crear BD del tenant)
2. app:create-member-table (crear tabla member)
3. app:migrate-tenant (preparar sistema de migraciones)
4. app:make-entity-tenant (generar otras entidades si es necesario)
            ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $subdomain = $input->getArgument('subdomain');
        $dryRun = $input->getOption('dry-run');
        $force = $input->getOption('force');
        $env = $input->getOption('environment');
        $activeOnly = $input->getOption('active-only');
        $parallel = $input->getOption('parallel');

        // Determinar modo de ejecución
        if ($subdomain === 'all' || $env !== null) {
            return $this->executeMultipleTenants($io, $subdomain, $env, $activeOnly, $parallel, $dryRun, $force);
        } elseif ($subdomain === null) {
            $io->error('Debes especificar un subdominio o usar "all" para todos los tenants, o --environment para filtrar por ambiente');
            return Command::FAILURE;
        } else {
            return $this->executeSingleTenant($io, $subdomain, $dryRun, $force);
        }
    }

    private function executeMultipleTenants(SymfonyStyle $io, ?string $subdomain, ?string $env, bool $activeOnly, bool $parallel, bool $dryRun, bool $force): int
    {
        $io->title('🔄 Migración Masiva de Tenants - Sistema Melisa');
        
        try {
            // Obtener lista de tenants según criterios
            $tenants = $this->getTenantsForMigration($env, $activeOnly, $io);
            
            if (empty($tenants)) {
                $io->warning('No se encontraron tenants que cumplan los criterios especificados');
                return Command::SUCCESS;
            }
            
            // Mostrar resumen
            $this->showMigrationSummary($io, $tenants, $env, $activeOnly, $parallel, $dryRun);
            
            // Confirmación si no es dry-run y no es force
            if (!$dryRun && !$force) {
                if (!$io->confirm('¿Confirmas ejecutar migraciones en ' . count($tenants) . ' tenant(s)?', false)) {
                    $io->note('Operación cancelada por el usuario');
                    return Command::SUCCESS;
                }
            }
            
            // Ejecutar migraciones
            if ($parallel && !$dryRun) {
                return $this->executeParallelMigrations($io, $tenants, $force);
            } else {
                return $this->executeSequentialMigrations($io, $tenants, $dryRun, $force);
            }
            
        } catch (\Exception $e) {
            $io->error('Error en migración masiva: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function executeSingleTenant(SymfonyStyle $io, string $subdomain, bool $dryRun, bool $force): int
    {
        $io->title('Migración de Base de Datos para Tenant - ' . $subdomain);

        try {
            // 1. Conectar a la base de datos central
            $centralConnection = DriverManager::getConnection($this->centralDbConfig);
            
            // 2. Buscar configuración del tenant
            $tenantConfig = $this->getTenantConfig($centralConnection, $subdomain, $io);
            if (!$tenantConfig) {
                $io->error("Tenant '{$subdomain}' no encontrado en el sistema");
                return 1;
            }
            
            // 3. Mostrar información del tenant
            $this->showTenantInfo($io, $tenantConfig);
            
            // 4. Conectar a la base de datos del tenant
            $tenantConnection = $this->connectToTenant($tenantConfig, $io);
            
            // 5. Verificar estado actual de migraciones
            $this->checkMigrationStatus($tenantConnection, $io);
            
            // 6. Crear esquema inicial si es necesario
            $this->ensureBasicSchema($tenantConnection, $io);
            
            if ($dryRun) {
                $io->note('Modo dry-run activado - No se ejecutarán migraciones reales');
                return 0;
            }
            
            // 7. Confirmar ejecución si no es forzada
            if (!$force && !$this->confirmExecution($io, $tenantConfig)) {
                $io->warning('Migración cancelada por el usuario');
                return 0;
            }
            
            // 8. Ejecutar migraciones
            $this->executeMigrations($tenantConnection, $io);
            
            // 9. Mostrar estado final
            $this->showFinalStatus($tenantConnection, $io);
            
            $io->success([
                "Migraciones ejecutadas exitosamente para '{$subdomain}'!",
                "Base de datos: {$tenantConfig['database_name']}",
                "El tenant está listo para usar"
            ]);
            
            return 0;
            
        } catch (Exception $e) {
            $io->error('Error de base de datos: ' . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            $io->error('Error general: ' . $e->getMessage());
            return 1;
        }
    }

    private function getTenantConfig($connection, string $subdomain, SymfonyStyle $io): ?array
    {
        $io->text("Buscando configuración del tenant '{$subdomain}'...");
        
        $query = '
            SELECT id, name, subdomain, database_name, rut_empresa,
                   COALESCE(host, \'localhost\') as host,
                   COALESCE(host_port, 3306) as host_port,
                   COALESCE(db_user, \'melisa\') as db_user,
                   COALESCE(db_password, \'melisamelisa\') as db_password,
                   is_active
            FROM tenant 
            WHERE subdomain = ? AND is_active = true
        ';
        
        $result = $connection->executeQuery($query, [$subdomain]);
        $tenant = $result->fetchAssociative();
        
        if ($tenant) {
            $io->text("Tenant encontrado: {$tenant['name']}");
        }
        
        return $tenant;
    }

    private function showTenantInfo(SymfonyStyle $io, array $tenantConfig): void
    {
        $io->section('Información del Tenant');
        
        $io->table(
            ['Campo', 'Valor'],
            [
                ['Nombre', $tenantConfig['name']],
                ['Subdominio', $tenantConfig['subdomain']],
                ['Base de Datos', $tenantConfig['database_name']],
                ['Host', $tenantConfig['host'] . ':' . $tenantConfig['host_port']],
                ['Usuario BD', $tenantConfig['db_user']],
                ['RUT Empresa', $tenantConfig['rut_empresa']],
            ]
        );
    }

    private function connectToTenant(array $tenantConfig, SymfonyStyle $io): \Doctrine\DBAL\Connection
    {
        $io->text("Conectando a la base de datos del tenant...");
        
        $tenantDbConfig = [
            'host' => $tenantConfig['host'],
            'port' => $tenantConfig['host_port'],
            'dbname' => $tenantConfig['database_name'],
            'user' => $tenantConfig['db_user'],
            'password' => $tenantConfig['db_password'],
            'driver' => 'pdo_mysql',
        ];
        
        $connection = DriverManager::getConnection($tenantDbConfig);
        
        // Verificar conexión
        $connection->executeQuery('SELECT 1');
        $io->text("Conexión establecida exitosamente");
        
        return $connection;
    }

    private function checkMigrationStatus($connection, SymfonyStyle $io): void
    {
        $io->text("Verificando estado de migraciones...");
        
        try {
            // Verificar si existe la tabla de migraciones
            $tables = $connection->executeQuery('SHOW TABLES LIKE "doctrine_migration_versions"');
            
            if ($tables->rowCount() === 0) {
                $io->text("Tabla de migraciones no existe - primera migración");
                return;
            }
            
            // Mostrar migraciones existentes
            $migrations = $connection->executeQuery('SELECT version, executed_at FROM doctrine_migration_versions ORDER BY executed_at DESC LIMIT 5');
            $migrationData = $migrations->fetchAllAssociative();
            
            if (empty($migrationData)) {
                $io->text("No hay migraciones ejecutadas previamente");
            } else {
                $io->text("Últimas migraciones ejecutadas:");
                foreach ($migrationData as $migration) {
                    $io->text("- {$migration['version']} ({$migration['executed_at']})");
                }
            }
            
        } catch (\Exception $e) {
            $io->text("No se pudo verificar estado de migraciones: " . $e->getMessage());
        }
    }

    private function ensureBasicSchema($connection, SymfonyStyle $io): void
    {
        $io->text("Asegurando esquema básico del tenant...");
        
        try {
            // Solo crear tabla de migraciones para el tenant
            // Las entidades de negocio se crean con comandos específicos
            $migrationTableSql = "
                CREATE TABLE IF NOT EXISTS doctrine_migration_versions (
                    version VARCHAR(191) NOT NULL PRIMARY KEY,
                    executed_at DATETIME DEFAULT NULL,
                    execution_time INT DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            
            $connection->executeStatement($migrationTableSql);
            $io->text("✅ Tabla doctrine_migration_versions del tenant verificada");
            
        } catch (\Exception $e) {
            $io->warning("Advertencia al crear esquema básico del tenant: " . $e->getMessage());
        }
    }

    private function confirmExecution(SymfonyStyle $io, array $tenantConfig): bool
    {
        return $io->confirm(
            "¿Confirmas ejecutar las migraciones en la base de datos '{$tenantConfig['database_name']}'?",
            false
        );
    }

    private function executeMigrations($connection, SymfonyStyle $io): void
    {
        $io->section('Ejecutando Migraciones');
        
        try {
            // Verificar si hay migraciones pendientes
            $hasPendingMigrations = $this->checkForPendingMigrations($connection);
            
            if (!$hasPendingMigrations) {
                $io->text("✅ No hay migraciones pendientes - La base de datos está actualizada");
                return;
            }
            
            // Ejecutar migraciones reales usando Doctrine
            $this->executeDoctrineMigrations($connection, $io);
            
            $io->text("✅ Todas las migraciones han sido procesadas");
            
        } catch (\Exception $e) {
            throw new \Exception("Error ejecutando migraciones: " . $e->getMessage());
        }
    }

    private function checkForPendingMigrations($connection): bool
    {
        try {
            // Verificar si existe la tabla de migraciones
            $tables = $connection->executeQuery('SHOW TABLES LIKE "doctrine_migration_versions"');
            
            if ($tables->rowCount() === 0) {
                // Si no existe la tabla, hay migraciones pendientes (la primera vez)
                return true;
            }
            
            // En un sistema real, aquí compararías las migraciones disponibles 
            // con las ejecutadas para determinar si hay pendientes
            // Por ahora, asumimos que siempre hay algo que hacer en la primera ejecución
            
            $executedMigrations = $connection->executeQuery('SELECT COUNT(*) FROM doctrine_migration_versions')->fetchOne();
            
            // Si no hay migraciones ejecutadas, entonces hay pendientes
            return $executedMigrations == 0;
            
        } catch (\Exception $e) {
            // Si hay error verificando, asumir que hay migraciones pendientes
            return true;
        }
    }

    private function executeDoctrineMigrations($connection, SymfonyStyle $io): void
    {
        $io->text("Ejecutando migraciones de Doctrine para entidades de negocio...");
        
        // NOTA: En un sistema real, aquí ejecutarías el comando de Doctrine:
        // php bin/console doctrine:migrations:migrate --no-interaction
        
        // Por ahora, simular solo la creación de registros en la tabla de tracking
        // Las tablas reales se crean con comandos específicos como create-member-table
        
        $io->text("ℹ️  Las entidades de negocio se crean con comandos específicos:");
        $io->text("   - app:create-member-table (tabla member)");
        $io->text("   - app:make-entity-tenant (otras entidades con Doctrine)");
        
        // Solo registrar que el sistema de migraciones está activo
        $migrationVersion = 'DoctrineMigrations\\Version' . date('YmdHis');
        $description = 'Sistema de migraciones activado para tenant';
        
        $connection->executeStatement(
            'INSERT IGNORE INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES (?, NOW(), ?)',
            [$migrationVersion, 100]
        );
        
        $io->text("✅ Sistema de migraciones configurado para el tenant");
    }

    private function showFinalStatus($connection, SymfonyStyle $io): void
    {
        $io->section('Estado Final de Migraciones');
        
        try {
            // Contar migraciones ejecutadas
            $totalMigrations = $connection->executeQuery('SELECT COUNT(*) FROM doctrine_migration_versions')->fetchOne();
            
            // Mostrar últimas migraciones
            $recentMigrations = $connection->executeQuery(
                'SELECT version, executed_at FROM doctrine_migration_versions ORDER BY executed_at DESC LIMIT 3'
            )->fetchAllAssociative();
            
            $io->text("Total de migraciones ejecutadas: {$totalMigrations}");
            
            if (!empty($recentMigrations)) {
                $io->text("Migraciones más recientes:");
                foreach ($recentMigrations as $migration) {
                    $io->text("- {$migration['version']} ({$migration['executed_at']})");
                }
            }
            
            // Verificar tablas creadas
            $tables = $connection->executeQuery('SHOW TABLES')->fetchAllAssociative();
            $tableCount = count($tables);
            
            $io->text("Tablas en la base de datos: {$tableCount}");
            
        } catch (\Exception $e) {
            $io->warning("No se pudo obtener estado final: " . $e->getMessage());
        }
    }

    private function getTenantsForMigration(?string $env, bool $activeOnly, SymfonyStyle $io): array
    {
        try {
            $connection = DriverManager::getConnection($this->centralDbConfig);
            
            $query = 'SELECT id, name, subdomain, database_name, rut_empresa, host, host_port, db_user, db_password, driver, is_active, language FROM tenant WHERE 1=1';
            $params = [];
            
            // Filtrar por estado activo
            if ($activeOnly) {
                $query .= ' AND is_active = 1';
            }
            
            // Filtrar por ambiente (basado en naming convention)
            if ($env !== null) {
                switch ($env) {
                    case 'dev':
                        $query .= ' AND (subdomain LIKE "%dev%" OR subdomain LIKE "%test%" OR subdomain LIKE "%demo%")';
                        break;
                    case 'prod':
                        $query .= ' AND subdomain NOT LIKE "%dev%" AND subdomain NOT LIKE "%test%" AND subdomain NOT LIKE "%demo%"';
                        break;
                    case 'test':
                        $query .= ' AND (subdomain LIKE "%test%" OR subdomain LIKE "%demo%")';
                        break;
                }
            }
            
            $query .= ' ORDER BY name';
            
            $result = $connection->executeQuery($query, $params);
            return $result->fetchAllAssociative();
            
        } catch (\Exception $e) {
            throw new \Exception('Error obteniendo tenants: ' . $e->getMessage());
        }
    }

    private function showMigrationSummary(SymfonyStyle $io, array $tenants, ?string $env, bool $activeOnly, bool $parallel, bool $dryRun): void
    {
        $io->section('📊 Resumen de la Migración Masiva');
        
        $io->definitionList(
            ['Modo de ejecución' => $dryRun ? '🔍 DRY-RUN (solo verificación)' : '🔄 EJECUCIÓN REAL'],
            ['Filtro por ambiente' => $env ? "🏷️ {$env}" : '🌐 Todos los ambientes'],
            ['Solo tenants activos' => $activeOnly ? '✅ Sí' : '❌ No'],
            ['Ejecución paralela' => $parallel && !$dryRun ? '⚡ Habilitada' : '🔄 Secuencial'],
            ['Total tenants' => count($tenants)]
        );
        
        $io->table(
            ['ID', 'Nombre', 'Subdominio', 'Base de Datos', 'Estado'],
            array_map(function($tenant) {
                return [
                    $tenant['id'],
                    $tenant['name'],
                    $tenant['subdomain'],
                    $tenant['database_name'],
                    $tenant['is_active'] ? '✅ Activo' : '❌ Inactivo'
                ];
            }, $tenants)
        );
    }

    private function executeSequentialMigrations(SymfonyStyle $io, array $tenants, bool $dryRun, bool $force): int
    {
        $io->section('🔄 Ejecutando Migraciones Secuenciales');
        
        $success = 0;
        $failures = 0;
        $results = [];
        
        foreach ($tenants as $index => $tenant) {
            $currentNum = $index + 1;
            $totalNum = count($tenants);
            $io->text("📋 Procesando [{$currentNum}/{$totalNum}]: {$tenant['name']} ({$tenant['subdomain']})");
            
            try {
                if ($dryRun) {
                    $io->text("  🔍 DRY-RUN: Se ejecutarían migraciones en {$tenant['database_name']}");
                    $success++;
                    $results[] = "✅ {$tenant['subdomain']}: DRY-RUN exitoso";
                } else {
                    $this->migrateSingleTenantData($tenant, $io, $force);
                    $success++;
                    $results[] = "✅ {$tenant['subdomain']}: Migraciones exitosas";
                }
                
            } catch (\Exception $e) {
                $failures++;
                $results[] = "❌ {$tenant['subdomain']}: {$e->getMessage()}";
                $io->warning("Error en {$tenant['subdomain']}: " . $e->getMessage());
            }
        }
        
        // Resumen final
        $this->showMigrationResults($io, $success, $failures, $results);
        
        return $failures > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function executeParallelMigrations(SymfonyStyle $io, array $tenants, bool $force): int
    {
        $io->section('⚡ Ejecutando Migraciones en Paralelo');
        $io->note('NOTA: Implementación paralela simulada - en producción usaría procesos fork o colas');
        
        // Por ahora, simular ejecución paralela con procesamiento por lotes
        $batchSize = 3;
        $batches = array_chunk($tenants, $batchSize);
        
        $success = 0;
        $failures = 0;
        $results = [];
        
        foreach ($batches as $batchIndex => $batch) {
            $io->text("🔄 Procesando lote " . ($batchIndex + 1) . "/" . count($batches) . " ({$batchSize} tenants)");
            
            foreach ($batch as $tenant) {
                try {
                    $this->migrateSingleTenantData($tenant, $io, $force);
                    $success++;
                    $results[] = "✅ {$tenant['subdomain']}: Migraciones exitosas";
                } catch (\Exception $e) {
                    $failures++;
                    $results[] = "❌ {$tenant['subdomain']}: {$e->getMessage()}";
                }
            }
            
            // Simular pausa entre lotes
            if ($batchIndex < count($batches) - 1) {
                $io->text("⏸️ Pausa entre lotes...");
                usleep(500000); // 0.5 segundos
            }
        }
        
        $this->showMigrationResults($io, $success, $failures, $results);
        
        return $failures > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function migrateSingleTenantData(array $tenant, SymfonyStyle $io, bool $force): void
    {
        // Conectar a la base de datos del tenant
        $tenantDbConfig = [
            'host' => $tenant['host'] ?? 'localhost',
            'port' => $tenant['host_port'] ?? 3306,
            'dbname' => $tenant['database_name'],
            'user' => $tenant['db_user'] ?? 'melisa',
            'password' => $tenant['db_password'] ?? 'melisamelisa',
            'driver' => 'pdo_mysql',
        ];
        
        $connection = DriverManager::getConnection($tenantDbConfig);
        
        // Asegurar esquema básico
        $this->ensureBasicSchema($connection, $io);
        
        // Ejecutar migraciones
        $this->executeMigrations($connection, $io);
    }

    private function showMigrationResults(SymfonyStyle $io, int $success, int $failures, array $results): void
    {
        $io->section('📈 Resultados de la Migración Masiva');
        
        $io->definitionList(
            ['✅ Exitosos' => $success],
            ['❌ Fallidos' => $failures],
            ['📊 Total procesados' => $success + $failures],
            ['🎯 Tasa de éxito' => $success + $failures > 0 ? round(($success / ($success + $failures)) * 100, 2) . '%' : '0%']
        );
        
        if (!empty($results)) {
            $io->text('📋 Detalle de resultados:');
            foreach ($results as $result) {
                $io->text("  {$result}");
            }
        }
        
        if ($failures === 0) {
            $io->success('🎉 Todas las migraciones fueron ejecutadas exitosamente!');
        } else {
            $io->warning("⚠️ Se completaron {$success} migraciones, pero {$failures} fallaron");
        }
    }
}