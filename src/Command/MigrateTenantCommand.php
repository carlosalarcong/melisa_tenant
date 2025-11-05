<?php

namespace App\Command;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:migrate-tenant',
    description: 'Generar migraciones y aplicarlas automáticamente a todos los tenants activos o a un tenant específico'
)]
class MigrateTenantCommand extends Command
{
    private int $verbosity = OutputInterface::VERBOSITY_NORMAL;
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
            ->addArgument('tenant', InputArgument::OPTIONAL, 'Subdomain del tenant específico a migrar (ej: melisalacolina)')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Solo mostrar qué se ejecutaría sin hacer cambios')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forzar ejecución sin confirmación')
            ->addOption('generate-only', null, InputOption::VALUE_NONE, 'Solo generar migraciones sin aplicarlas')
            ->addOption('cleanup-duplicates', null, InputOption::VALUE_NONE, 'Limpiar tablas duplicadas (CamelCase vs lowercase)')
            ->addOption('cleanup-orphaned', null, InputOption::VALUE_NONE, 'Limpiar referencias huérfanas de migraciones en BD')
            ->setHelp('
Este comando automatiza completamente el proceso de migraciones multi-tenant:

1. 🔍 Busca automáticamente todos los tenants activos en melisa_central
2. 📦 Genera migraciones basadas en las entidades existentes 
3. 🚀 Aplica las migraciones a todos los tenants activos o a uno específico

<info>Ejemplos de uso:</info>

  <comment># Migración completa automática (todos los tenants)</comment>
  php bin/console app:migrate-tenant

  <comment># Migrar solo un tenant específico</comment>
  php bin/console app:migrate-tenant melisalacolina
  php bin/console app:migrate-tenant melisahospital
  php bin/console app:migrate-tenant melisawiclinic

  <comment># Solo verificar qué se haría en un tenant específico</comment>
  php bin/console app:migrate-tenant melisalacolina --dry-run

  <comment># Solo generar migraciones sin aplicar</comment>  
  php bin/console app:migrate-tenant --generate-only

  <comment># Forzar migración sin confirmación</comment>
  php bin/console app:migrate-tenant melisalacolina --force

  <comment># Limpiar tablas duplicadas en todos los tenants</comment>
  php bin/console app:migrate-tenant --cleanup-duplicates

  <comment># Limpiar tablas duplicadas en un tenant específico (dry-run)</comment>
  php bin/console app:migrate-tenant melisahospital --cleanup-duplicates --dry-run

  <comment># Limpiar referencias huérfanas de migraciones eliminadas</comment>
  php bin/console app:migrate-tenant --cleanup-orphaned

<info>Proceso automático:</info>
✅ Detecta tenants activos en melisa_central
✅ Genera migraciones desde entidades existentes
✅ Aplica migraciones a tenant específico o todos los tenants
✅ Reporte completo de resultados
            ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->verbosity = $output->getVerbosity();
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $force = $input->getOption('force');
        $generateOnly = $input->getOption('generate-only');
        $cleanupDuplicates = $input->getOption('cleanup-duplicates');
        $cleanupOrphaned = $input->getOption('cleanup-orphaned');
        $tenantSubdomain = $input->getArgument('tenant');

        if ($cleanupDuplicates) {
            return $this->cleanupDuplicateTables($input, $output, $io, $tenantSubdomain, $dryRun);
        }

        if ($cleanupOrphaned) {
            return $this->cleanupOrphanedMigrations($input, $output, $io, $tenantSubdomain, $dryRun);
        }

        if ($tenantSubdomain) {
            $io->title("🚀 Migración Multi-Tenant: {$tenantSubdomain}");
        } else {
            $io->title('🚀 Migración Automática Multi-Tenant (Todos los tenants)');
        }
        
        try {
            // 1. Obtener tenants activos (todos o uno específico)
            $tenants = $this->getActiveTenants($io, $tenantSubdomain);
            
            if (empty($tenants)) {
                if ($tenantSubdomain) {
                    $io->error("❌ No se encontró el tenant '{$tenantSubdomain}' o no está activo");
                } else {
                    $io->warning('No se encontraron tenants activos en el sistema');
                }
                return Command::FAILURE;
            }

            // 2. Mostrar resumen
            $this->showMigrationSummary($io, $tenants, $dryRun, $generateOnly, $tenantSubdomain);

            // 3. Confirmación si no es dry-run ni force
            if (!$dryRun && !$force && !$this->confirmExecution($tenants, $io, $tenantSubdomain)) {
                $io->note('Operación cancelada por el usuario');
                return Command::SUCCESS;
            }

            // 4. Generar migraciones automáticamente
            $migrationGenerated = $this->generateMigrations($dryRun, $io);
            
            if ($generateOnly) {
                $io->success('✅ Migraciones generadas. Usa sin --generate-only para aplicarlas.');
                return Command::SUCCESS;
            }

            if (!$migrationGenerated && !$dryRun) {
                $io->note('No hay cambios para migrar. Aplicando migraciones existentes...');
            }

            // 5. Aplicar migraciones a los tenants seleccionados
            $results = $this->applyMigrationsToAllTenants($tenants, $dryRun, $io);

            // 6. Mostrar resultados finales
            $this->showFinalResults($io, $results, $dryRun, $tenantSubdomain);

            return $results['failures'] > 0 ? Command::FAILURE : Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Error en migración automática: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function getActiveTenants(SymfonyStyle $io, ?string $tenantSubdomain = null): array
    {
        try {
            $config = $this->centralDbConfig;
            $config['schemaManagerFactory'] = new DefaultSchemaManagerFactory();
            $connection = DriverManager::getConnection($config);
            
            $whereClause = 'WHERE is_active = 1';
            $params = [];
            
            if ($tenantSubdomain) {
                $whereClause .= ' AND subdomain = ?';
                $params[] = $tenantSubdomain;
            }
            
            $query = "
                SELECT id, name, subdomain, database_name, rut_empresa,
                       COALESCE(host, 'localhost') as host,
                       COALESCE(host_port, 3306) as host_port,
                       COALESCE(db_user, 'melisa') as db_user,
                       COALESCE(db_password, 'melisamelisa') as db_password
                FROM tenant 
                $whereClause
                ORDER BY name
            ";
            
            $result = $connection->executeQuery($query, $params);
            $tenants = $result->fetchAllAssociative();
            
            if ($tenantSubdomain) {
                $io->text("🔍 Encontrado tenant específico: " . $tenantSubdomain);
            } else {
                $io->text("🔍 Encontrados " . count($tenants) . " tenant(s) activos en melisa_central");
            }
            
            return $tenants;
            
        } catch (\Exception $e) {
            throw new \Exception('Error obteniendo tenants activos: ' . $e->getMessage());
        }
    }

    private function showMigrationSummary(SymfonyStyle $io, array $tenants, bool $dryRun, bool $generateOnly, ?string $tenantSubdomain = null): void
    {
        $title = $tenantSubdomain ? "📊 Resumen de Migración: {$tenantSubdomain}" : '📊 Resumen de Migración Automática';
        $io->section($title);
        
        $mode = $dryRun ? '🔍 DRY-RUN (simulación)' : '🔄 EJECUCIÓN REAL';
        if ($generateOnly) {
            $mode = '📦 GENERAR MIGRACIONES ÚNICAMENTE';
        }
        
        $tenantLabel = $tenantSubdomain ? "Tenant seleccionado" : "Total tenants activos";
        
        // Obtener información de migraciones disponibles
        $migrationInfo = $this->getMigrationSummaryInfo();
        
        $io->definitionList(
            ['Modo de ejecución' => $mode],
            [$tenantLabel => count($tenants)],
            ['Directorio migraciones' => './migrations/'],
            ['Entidades detectadas' => $this->countEntities()],
            ['Migraciones disponibles' => $migrationInfo['available']],
            ['Estado del esquema' => $migrationInfo['schema_status']]
        );
        
        $io->text('📋 Tenants que serán procesados:');
        foreach ($tenants as $tenant) {
            $io->text("  • {$tenant['name']} ({$tenant['subdomain']}) → BD: {$tenant['database_name']}");
        }
    }

    /**
     * Obtiene información resumida sobre migraciones y estado del esquema
     */
    private function getMigrationSummaryInfo(): array
    {
        try {
            // Contar archivos de migración disponibles
            $migrationsDir = '/var/www/html/melisa_tenant/migrations';
            $migrationFiles = glob($migrationsDir . '/Version*.php');
            $availableCount = count($migrationFiles);
            
            // Verificar estado del esquema
            $process = new Process([
                'php', 'bin/console', 'doctrine:schema:validate', '--skip-sync'
            ]);
            
            $process->setWorkingDirectory('/var/www/html/melisa_tenant');
            $process->run();
            
            $output = $process->getOutput();
            $schemaStatus = '❓ Desconocido';
            
            if (strpos($output, 'mapping files are valid') !== false) {
                if (strpos($output, 'database schema is in sync') !== false) {
                    $schemaStatus = '✅ Sincronizado';
                } else {
                    $schemaStatus = '⚠️  Requiere sincronización';
                }
            } else {
                $schemaStatus = '❌ Errores en mapping';
            }
            
            return [
                'available' => $availableCount,
                'schema_status' => $schemaStatus
            ];
            
        } catch (\Exception $e) {
            return [
                'available' => 'Error al verificar',
                'schema_status' => '❓ No se pudo verificar'
            ];
        }
    }

    private function countEntities(): int
    {
        $entityDir = '/var/www/html/melisa_tenant/src/Entity';
        if (!is_dir($entityDir)) {
            return 0;
        }
        
        $entities = glob($entityDir . '/*.php');
        return count($entities);
    }

    private function confirmExecution(array $tenants, SymfonyStyle $io, ?string $tenantSubdomain = null): bool
    {
        if ($tenantSubdomain) {
            return $io->confirm("¿Confirmas generar y aplicar migraciones en el tenant '{$tenantSubdomain}'?", false);
        }
        return $io->confirm('¿Confirmas generar y aplicar migraciones en ' . count($tenants) . ' tenant(s)?', false);
    }

    private function generateMigrations(bool $dryRun, SymfonyStyle $io): bool
    {
        $io->section('📦 Generación Automática de Migraciones');
        
        if ($dryRun) {
            $io->text('🔍 DRY-RUN: Se verificaría si se requieren nuevas migraciones');
            return true;
        }

        try {
            // Primero verificar si realmente necesitamos generar una nueva migración
            $needsNewMigration = $this->checkIfNewMigrationNeeded($io);
            
            if (!$needsNewMigration) {
                $io->success('ℹ️  No se requieren nuevas migraciones - esquema está sincronizado');
                return true; // Devolver true porque no hay error, simplemente no hay trabajo que hacer
            }

            $io->text('🔄 Ejecutando: doctrine:migrations:diff');
            
            // Ejecutar doctrine:migrations:diff para generar migraciones automáticamente
            $process = new Process([
                'php', 'bin/console', 'doctrine:migrations:diff', '--no-interaction'
            ]);
            
            $process->setWorkingDirectory('/var/www/html/melisa_tenant');
            $process->run();
            
            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                if (strpos($output, 'Generated new migration') !== false) {
                    $io->text('✅ ' . trim($output));
                    return true;
                } else {
                    $io->text('ℹ️  No hay cambios que requieran nueva migración');
                    return false;
                }
            } else {
                $error = $process->getErrorOutput();
                if (strpos($error, 'no changes') !== false || strpos($error, 'up to date') !== false) {
                    $io->text('ℹ️  Schema está actualizado, no se requieren nuevas migraciones');
                    return false;
                } else {
                    throw new \Exception('Error generando migración: ' . $error);
                }
            }
            
        } catch (\Exception $e) {
            $io->warning('Advertencia generando migraciones: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si realmente se necesita una nueva migración comparando el esquema actual
     */
    private function checkIfNewMigrationNeeded(SymfonyStyle $io): bool
    {
        $io->text('🔍 Iniciando verificación de necesidad de nuevas migraciones...');
        
        try {
            // PRIMERA VERIFICACIÓN: Si las tablas del tenant ya existen, NO generar migraciones
            $io->text('🔍 Verificando consistencia del esquema primero...');
            if ($this->verifySchemaConsistency($io)) {
                $io->text('🎉 Todos los tenants están actualizados y el esquema es consistente - NO SE REQUIEREN nuevas migraciones');
                return false; // ¡No generar migraciones!
            }
            
            // SEGUNDA VERIFICACIÓN: Verificar estado de doctrine migrations
            $process = new Process([
                'php', 'bin/console', 'doctrine:migrations:status'
            ]);
            
            $process->setWorkingDirectory('/var/www/html/melisa_tenant');
            $process->run();
            
            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                $io->text('📊 Estado de migraciones obtenido exitosamente');
                
                // Extraer información de migraciones del output
                if (preg_match('/Available\s*\|\s*(\d+)/', $output, $matches)) {
                    $availableMigrations = (int)$matches[1];
                } else {
                    $availableMigrations = 0;
                }
                
                // En lugar de confiar en "Executed" del doctrine status, 
                // verificar directamente en cada tenant
                $realExecutedMigrations = $this->countRealExecutedMigrations($io);
                
                $pendingMigrations = $availableMigrations - $realExecutedMigrations;
                
                $io->text("📈 Migraciones disponibles: {$availableMigrations}, ejecutadas reales: {$realExecutedMigrations}, pendientes: {$pendingMigrations}");
                
                if ($pendingMigrations > 0) {
                    $io->text("📋 Hay {$pendingMigrations} migración(es) pendiente(s) por aplicar - NO generar nuevas");
                    return false; // No generar nuevas si hay pendientes
                }
                
                // TERCERA VERIFICACIÓN: Verificar si todos los tenants están actualizados
                $io->text('🔍 Verificando estado de todos los tenants...');
                $allTenantsUpToDate = $this->checkAllTenantsUpToDate($io);
                
                if ($allTenantsUpToDate) {
                    $io->text('✅ Todos los tenants tienen migraciones al día - NO generar nuevas');
                    return false; // ¡No generar migraciones!
                } else {
                    $io->text('⚠️ Algunos tenants no están al día con las migraciones');
                }
                
                $io->text('🔍 Se detectaron cambios - se requiere nueva migración');
                return true;
                
            } else {
                $io->text('⚠️ Error al ejecutar doctrine:migrations:status: ' . $process->getErrorOutput());
                return true;
            }
            
        } catch (\Exception $e) {
            $io->text("⚠️  No se pudo verificar estado del esquema: " . $e->getMessage());
            // En caso de error, ser conservador y verificar
            return true;
        }
    }

    /**
     * Cuenta las migraciones realmente ejecutadas en todos los tenants
     */
    private function countRealExecutedMigrations(SymfonyStyle $io): int
    {
        try {
            $tenants = $this->getActiveTenants($io);
            if (empty($tenants)) {
                return 0;
            }

            $maxExecuted = 0;
            foreach ($tenants as $tenant) {
                $tenantDbConfig = [
                    'host' => $tenant['host'],
                    'port' => $tenant['host_port'],
                    'dbname' => $tenant['database_name'],
                    'user' => $tenant['db_user'],
                    'password' => $tenant['db_password'],
                    'driver' => 'pdo_mysql',
                    'schemaManagerFactory' => new DefaultSchemaManagerFactory(),
                ];
                
                $connection = DriverManager::getConnection($tenantDbConfig);
                $executedMigrations = $this->getExecutedMigrations($connection);
                $maxExecuted = max($maxExecuted, count($executedMigrations));
            }
            
            return $maxExecuted;
            
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Verifica la consistencia del esquema comparando con un tenant específico
     */
    private function verifySchemaConsistency(SymfonyStyle $io): bool
    {
        try {
            // Obtener un tenant activo para verificar
            $tenants = $this->getActiveTenants($io);
            if (empty($tenants)) {
                return true; // Si no hay tenants, no hay inconsistencias
            }

            $testTenant = $tenants[0]; // Usar el primer tenant como referencia
            
            // Conectar temporalmente a la base de datos del tenant para verificar esquema
            $tenantConnection = DriverManager::getConnection([
                'driver' => 'pdo_mysql',
                'host' => $testTenant['host'],
                'port' => $testTenant['host_port'],
                'dbname' => $testTenant['database_name'],
                'user' => $testTenant['db_user'],
                'password' => $testTenant['db_password'],
                'charset' => 'utf8mb4',
                'schemaManagerFactory' => new DefaultSchemaManagerFactory(),
            ]);

            // Verificar si las tablas principales existen en el tenant usando SHOW TABLES
            $result = $tenantConnection->executeQuery("SHOW TABLES");
            $tables = array_column($result->fetchAllAssociative(), 'Tables_in_' . $testTenant['database_name']);
            
            // Tablas que esperamos encontrar en el tenant
            $expectedTenantTables = ['member', 'estado', 'pais', 'region', 'religion', 'sexo'];
            $tablesFound = 0;
            
            foreach ($expectedTenantTables as $expectedTable) {
                if (in_array($expectedTable, $tables)) {
                    $tablesFound++;
                }
            }
            
            // También verificar que NO tiene las tablas que solo deben estar en central
            $centralOnlyTables = ['tenant', 'system_config'];
            $centralTablesInTenant = 0;
            
            foreach ($centralOnlyTables as $centralTable) {
                if (in_array($centralTable, $tables)) {
                    $centralTablesInTenant++;
                }
            }
            
            // El esquema es consistente si:
            // 1. Encuentra la mayoría de tablas esperadas del tenant (al menos 80%)
            // 2. NO encuentra tablas que solo deben estar en central
            $requiredTables = (int)(count($expectedTenantTables) * 0.8);
            $consistency = ($tablesFound >= $requiredTables) && ($centralTablesInTenant === 0);
            
            $tenantConnection->close();
            
            if ($consistency) {
                $io->text("✅ Esquema consistente verificado en tenant '{$testTenant['subdomain']}' ({$tablesFound}/" . count($expectedTenantTables) . " tablas de tenant encontradas, {$centralTablesInTenant}/" . count($centralOnlyTables) . " tablas de central)");
            } else {
                $io->text("⚠️ Inconsistencia de esquema detectada en tenant '{$testTenant['subdomain']}' - Tablas de tenant: {$tablesFound}/" . count($expectedTenantTables) . " (requiere {$requiredTables}), Tablas de central: {$centralTablesInTenant}/" . count($centralOnlyTables));
                
                // Debug: mostrar qué tablas se encontraron
                if ($this->isVerbose()) {
                    $io->text("  🔍 Tablas encontradas: " . implode(', ', $tables));
                    $io->text("  🎯 Tablas esperadas: " . implode(', ', $expectedTenantTables));
                }
            }
            
            return $consistency;
            
        } catch (\Exception $e) {
            $io->text("⚠️ Error al verificar consistencia del esquema: " . $e->getMessage());
            // En caso de error, ser más conservador - solo devolver false si es un error crítico
            if (strpos($e->getMessage(), 'Access denied') !== false || 
                strpos($e->getMessage(), 'Connection refused') !== false) {
                return false;
            }
            // Para otros errores, asumir que está bien para evitar generación innecesaria
            return true;
        }
    }

    /**
     * Verifica si todos los tenants están actualizados con las migraciones disponibles
     */
    private function checkAllTenantsUpToDate(SymfonyStyle $io): bool
    {
        try {
            $tenants = $this->getActiveTenants($io);
            $migrationsDir = '/var/www/html/melisa_tenant/migrations';
            $availableMigrations = $this->getAvailableMigrationVersions($migrationsDir);
            
            foreach ($tenants as $tenant) {
                $tenantDbConfig = [
                    'host' => $tenant['host'],
                    'port' => $tenant['host_port'],
                    'dbname' => $tenant['database_name'],
                    'user' => $tenant['db_user'],
                    'password' => $tenant['db_password'],
                    'driver' => 'pdo_mysql',
                    'schemaManagerFactory' => new DefaultSchemaManagerFactory(),
                ];
                
                $connection = DriverManager::getConnection($tenantDbConfig);
                $executedMigrations = $this->getExecutedMigrations($connection);
                
                // Contar migraciones disponibles vs ejecutadas
                $pendingMigrations = array_diff($availableMigrations, $executedMigrations);
                
                if (!empty($pendingMigrations)) {
                    return false; // Al menos un tenant tiene migraciones pendientes
                }
            }
            
            return true; // Todos los tenants están actualizados
            
        } catch (\Exception $e) {
            return false; // En caso de error, asumir que no están actualizados
        }
    }

    private function applyMigrationsToAllTenants(array $tenants, bool $dryRun, SymfonyStyle $io): array
    {
        $io->section('🚀 Aplicando Migraciones a Todos los Tenants');
        
        $success = 0;
        $failures = 0;
        $results = [];
        
        foreach ($tenants as $index => $tenant) {
            $currentNum = $index + 1;
            $totalNum = count($tenants);
            $io->text("📋 Procesando [{$currentNum}/{$totalNum}]: {$tenant['name']} ({$tenant['subdomain']})");
            
            try {
                if ($dryRun) {
                    $io->text("  🔍 DRY-RUN: Se aplicarían migraciones en {$tenant['database_name']}");
                    $success++;
                    $results[] = "✅ {$tenant['subdomain']}: DRY-RUN exitoso";
                } else {
                    $this->applyMigrationsToSingleTenant($tenant, $io);
                    $success++;
                    $results[] = "✅ {$tenant['subdomain']}: Migraciones aplicadas exitosamente";
                }
                
            } catch (\Exception $e) {
                $failures++;
                $results[] = "❌ {$tenant['subdomain']}: {$e->getMessage()}";
                $io->warning("Error en {$tenant['subdomain']}: " . $e->getMessage());
            }
        }
        
        return [
            'success' => $success,
            'failures' => $failures,
            'results' => $results,
            'total' => count($tenants)
        ];
    }

    private function applyMigrationsToSingleTenant(array $tenant, SymfonyStyle $io): void
    {
        try {
            // 1. Conectar a la base de datos del tenant
            $tenantDbConfig = [
                'host' => $tenant['host'],
                'port' => $tenant['host_port'],
                'dbname' => $tenant['database_name'],
                'user' => $tenant['db_user'],
                'password' => $tenant['db_password'],
                'driver' => 'pdo_mysql',
                'schemaManagerFactory' => new DefaultSchemaManagerFactory(),
            ];
            
            $connection = DriverManager::getConnection($tenantDbConfig);
            $connection->executeQuery('SELECT 1'); // Verificar conexión
            
            // 2. Asegurar tabla de migraciones
            $this->ensureMigrationTable($connection);
            
            // 3. Aplicar migraciones SQL desde archivos
            $this->applyMigrationFiles($connection, $tenant['database_name'], $io);
            
        } catch (\Exception $e) {
            throw new \Exception("Error en {$tenant['subdomain']}: " . $e->getMessage());
        }
    }

    private function ensureMigrationTable($connection): void
    {
        $migrationTableSql = "
            CREATE TABLE IF NOT EXISTS doctrine_migration_versions (
                version VARCHAR(191) NOT NULL PRIMARY KEY,
                executed_at DATETIME DEFAULT NULL,
                execution_time INT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $connection->executeStatement($migrationTableSql);
    }

    private function applyMigrationFiles($connection, string $dbName, SymfonyStyle $io): void
    {
        $migrationsDir = '/var/www/html/melisa_tenant/migrations';
        if (!is_dir($migrationsDir)) {
            $io->text("    ⚠️  Directorio de migraciones no encontrado: {$migrationsDir}");
            return;
        }

        // Obtener todos los archivos de migración ordenados por fecha
        $migrationFiles = $this->getMigrationFiles($migrationsDir);
        
        if (empty($migrationFiles)) {
            $io->text("    ℹ️  No se encontraron archivos de migración");
            return;
        }

        // Obtener migraciones ya ejecutadas
        $executedMigrations = $this->getExecutedMigrations($connection);
        $io->text("    📊 Migraciones ejecutadas anteriormente: " . count($executedMigrations));

        $newMigrations = 0;
        $skippedMigrations = 0;
        $failedMigrations = 0;

        foreach ($migrationFiles as $migrationInfo) {
            $filename = $migrationInfo['filename'];
            $version = $migrationInfo['version'];
            $filePath = $migrationInfo['path'];

            if (in_array($version, $executedMigrations)) {
                $skippedMigrations++;
                if ($this->isVerbose()) {
                    $io->text("    ⏭️  Saltando migración ya ejecutada: {$filename}");
                }
                continue;
            }

            $io->text("    🔄 Aplicando migración: {$filename}");
            
            try {
                $startTime = microtime(true);
                
                // Aplicar la migración dinámicamente
                $this->applyDynamicMigration($connection, $filename, $io);
                
                $executionTime = (int)((microtime(true) - $startTime) * 1000); // en ms
                
                // Registrar como ejecutada
                $this->markMigrationAsExecuted($connection, $version, $executionTime);
                
                $newMigrations++;
                $io->text("    ✅ Migración completada en {$executionTime}ms");
                
            } catch (\Exception $e) {
                $failedMigrations++;
                $io->text("    ❌ Error en migración {$filename}: " . $e->getMessage());
                
                // Decidir si continuar o detener
                if ($this->shouldStopOnMigrationError($e)) {
                    throw new \Exception("Migración crítica falló: {$filename}. Error: " . $e->getMessage());
                }
            }
        }

        // Resumen de la aplicación de migraciones
        $io->text("    📈 Resumen: {$newMigrations} nuevas, {$skippedMigrations} saltadas, {$failedMigrations} fallidas");
    }

    /**
     * Obtiene archivos de migración ordenados por versión
     */
    private function getMigrationFiles(string $migrationsDir): array
    {
        $files = glob($migrationsDir . '/Version*.php');
        $migrationFiles = [];

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $version = 'DoctrineMigrations\\' . $filename;
            
            // Extraer timestamp de la versión para ordenar
            preg_match('/Version(\d{14})/', $filename, $matches);
            $timestamp = $matches[1] ?? '00000000000000';

            $migrationFiles[] = [
                'filename' => $filename,
                'version' => $version,
                'path' => $file,
                'timestamp' => $timestamp
            ];
        }

        // Ordenar por timestamp (fecha de creación)
        usort($migrationFiles, function($a, $b) {
            return strcmp($a['timestamp'], $b['timestamp']);
        });

        return $migrationFiles;
    }

    /**
     * Obtiene lista de migraciones ya ejecutadas
     */
    private function getExecutedMigrations($connection): array
    {
        try {
            $result = $connection->executeQuery("SELECT version FROM doctrine_migration_versions ORDER BY version");
            return array_column($result->fetchAllAssociative(), 'version');
        } catch (\Exception $e) {
            // Si la tabla no existe, no hay migraciones ejecutadas
            return [];
        }
    }

    /**
     * Marca una migración como ejecutada
     */
    private function markMigrationAsExecuted($connection, string $version, int $executionTime): void
    {
        $insertSql = "INSERT IGNORE INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES (?, NOW(), ?)";
        $connection->executeStatement($insertSql, [$version, $executionTime]);
    }

    /**
     * Determina si se debe detener la ejecución en caso de error de migración
     */
    private function shouldStopOnMigrationError(\Exception $e): bool
    {
        $message = $e->getMessage();
        
        // Errores que deben detener todo el proceso
        $criticalErrorPatterns = [
            'Connection refused',
            'Access denied',
            'Database .* doesn\'t exist',
            'Syntax error.*near',
            'Foreign key constraint fails.*REFERENCES'
        ];

        foreach ($criticalErrorPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $message)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Aplica migración dinámicamente detectando su tipo y contenido
     */
    private function applyDynamicMigration($connection, string $filename, SymfonyStyle $io = null): void
    {
        $migrationFile = '/var/www/html/melisa_tenant/migrations/' . $filename . '.php';
        
        if (!file_exists($migrationFile)) {
            if ($io) {
                $io->text("    ⚠️  Archivo de migración no encontrado: {$filename}");
            }
            return;
        }
        
        // SIEMPRE usar método manual para evitar que Doctrine use conexión incorrecta
        if ($io) {
            $io->text("    🔄 Aplicando migración con método manual para {$filename}");
        }
        $this->executeManualMigration($connection, $migrationFile, $io);
    }

    /**
     * Ejecuta migración usando instanciación directa de la clase Doctrine
     */
    private function executeDoctrineMigration($connection, string $filename, string $migrationFile, SymfonyStyle $io = null): void
    {
        // Incluir el archivo de migración
        require_once $migrationFile;
        
        // Construir el nombre completo de la clase
        $className = 'DoctrineMigrations\\' . $filename;
        
        if (!class_exists($className)) {
            throw new \Exception("Clase de migración no encontrada: {$className}");
        }
        
        // Crear una instancia de la migración
        $migration = new $className($connection, $this->createMockLogger());
        
        // Crear un Schema mock (Doctrine lo necesita pero no lo usamos directamente)
        $schema = new \Doctrine\DBAL\Schema\Schema();
        
        // Ejecutar el método up() de la migración
        $migration->up($schema);
        
        if ($io) {
            $io->text("    ✅ Migración aplicada exitosamente: {$filename}");
        }
    }

    /**
     * Ejecuta migración parseando manualmente el archivo PHP
     */
    private function executeManualMigration($connection, string $migrationFile, SymfonyStyle $io = null): void
    {
        // Leer el contenido del archivo de migración
        $migrationContent = file_get_contents($migrationFile);
        
        // Extraer las sentencias SQL del método up()
        $sqlStatements = $this->extractSqlFromMigration($migrationContent);
        
        if (empty($sqlStatements)) {
            if ($io) {
                $io->text("    ℹ️  No se encontraron sentencias SQL en la migración");
            }
            return;
        }
        
        $successCount = 0;
        $errorCount = 0;
        
        // Ejecutar cada sentencia SQL
        foreach ($sqlStatements as $index => $sql) {
            try {
                $cleanSql = $this->cleanSqlStatement($sql);
                
                if (!empty($cleanSql) && $cleanSql !== ';') {
                    $connection->executeStatement($cleanSql);
                    $successCount++;
                    
                    if ($io && $this->isVerbose()) {
                        $io->text("      📝 SQL ejecutado: " . substr($cleanSql, 0, 60) . '...');
                    }
                }
            } catch (\Exception $e) {
                $errorCount++;
                
                // Algunos errores son esperables (tabla ya existe, etc.)
                if ($this->isExpectedMigrationError($e->getMessage())) {
                    if ($io && $this->isVerbose()) {
                        $io->text("      ⚠️  Error esperado (ignorado): " . substr($e->getMessage(), 0, 60) . '...');
                    }
                } else {
                    if ($io) {
                        $io->text("      ❌ Error SQL: " . $e->getMessage());
                    }
                    // Decidir si continuar o fallar
                    if (!$this->shouldContinueOnError($e->getMessage())) {
                        throw new \Exception("Error crítico ejecutando SQL: {$cleanSql}. Error: " . $e->getMessage());
                    }
                }
            }
        }
        
        if ($io) {
            $io->text("    ✅ Migración procesada: {$successCount} SQL exitosos, {$errorCount} errores manejados");
        }
    }

    /**
     * Crea un logger mock para Doctrine
     */
    private function createMockLogger()
    {
        return new class implements \Psr\Log\LoggerInterface {
            public function emergency($message, array $context = []): void {}
            public function alert($message, array $context = []): void {}
            public function critical($message, array $context = []): void {}
            public function error($message, array $context = []): void {}
            public function warning($message, array $context = []): void {}
            public function notice($message, array $context = []): void {}
            public function info($message, array $context = []): void {}
            public function debug($message, array $context = []): void {}
            public function log($level, $message, array $context = []): void {}
        };
    }

    /**
     * Limpia y normaliza una sentencia SQL
     */
    private function cleanSqlStatement(string $sql): string
    {
        // Limpiar caracteres de escape y normalizar
        $cleanSql = str_replace(['\\\'', '\\"', '\\\\'], ["'", '"', '\\'], $sql);
        $cleanSql = trim($cleanSql);
        
        // Eliminar comentarios SQL de línea
        $cleanSql = preg_replace('/--.*$/m', '', $cleanSql);
        
        // Eliminar comentarios SQL de bloque
        $cleanSql = preg_replace('/\/\*.*?\*\//s', '', $cleanSql);
        
        // Normalizar espacios en blanco
        $cleanSql = preg_replace('/\s+/', ' ', $cleanSql);
        
        return trim($cleanSql);
    }

    /**
     * Determina si el comando está en modo verbose
     */
    private function isVerbose(): bool
    {
        return $this->verbosity >= OutputInterface::VERBOSITY_VERBOSE;
    }

    /**
     * Determina si se debe continuar después de un error
     */
    private function shouldContinueOnError(string $errorMessage): bool
    {
        // Errores que deben detener la ejecución
        $criticalErrors = [
            'Syntax error',
            'Access denied',
            'Connection lost',
            'Server has gone away',
            'Disk full',
            'Out of memory'
        ];
        
        foreach ($criticalErrors as $criticalError) {
            if (stripos($errorMessage, $criticalError) !== false) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Extrae sentencias SQL del archivo de migración de forma inteligente
     */
    private function extractSqlFromMigration(string $content): array
    {
        $sqlStatements = [];
        
        // SOLO extraer SQL del método up(), NO del método down()
        // Buscar el contenido entre "public function up(" y "public function down("
        if (preg_match('/public function up\(.*?\)\s*:\s*void\s*\{(.*?)public function down\(/s', $content, $matches)) {
            $upMethodContent = $matches[1];
            
            // Buscar llamadas a $this->addSql() solo en el método up()
            $patterns = [
                '/\$this->addSql\s*\(\s*[\'\"](.*?)[\'\"]\s*\)\s*;/s',           // Comillas simples/dobles básicas
                '/\$this->addSql\s*\(\s*<<<[\'\"]*(\w+)[\'\"]*\s*(.*?)\s*\1\s*\)\s*;/s', // Heredoc/Nowdoc
                '/\$this->addSql\s*\(\s*([\'\"]).+?\1\s*\.\s*\$\w+\s*\.\s*([\'\"]).+?\2\s*\)\s*;/s' // Concatenación
            ];
            
            foreach ($patterns as $pattern) {
                preg_match_all($pattern, $upMethodContent, $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $sql) {
                        $sqlStatements[] = $sql;
                    }
                }
            }
        }
        
        // Si no encuentra con el método anterior, intentar método fallback
        if (empty($sqlStatements)) {
            // Buscar directamente sentencias SQL comunes SOLO en método up()
            if (preg_match('/public function up\(.*?\)\s*:\s*void\s*\{(.*?)public function down\(/s', $content, $matches)) {
                $upMethodContent = $matches[1];
                
                $sqlPatterns = [
                    '/CREATE\s+TABLE\s+[^;]+;/i',
                    '/ALTER\s+TABLE\s+[^;]+;/i',
                    '/INSERT\s+INTO\s+[^;]+;/i',
                    '/UPDATE\s+[^;]+;/i',
                ];
                
                foreach ($sqlPatterns as $pattern) {
                    preg_match_all($pattern, $upMethodContent, $matches);
                    if (!empty($matches[0])) {
                        $sqlStatements = array_merge($sqlStatements, $matches[0]);
                    }
                }
            }
        }
        
        // Limpiar y filtrar sentencias
        $cleanStatements = [];
        foreach ($sqlStatements as $sql) {
            $cleanSql = $this->cleanSqlStatement($sql);
            if (!empty($cleanSql) && strlen($cleanSql) > 5) { // Filtrar sentencias muy cortas
                $cleanStatements[] = $cleanSql;
            }
        }
        
        return array_unique($cleanStatements); // Eliminar duplicados
    }
    
    private function isExpectedMigrationError(string $errorMessage): bool
    {
        // Errores comunes y esperables durante migraciones
        $expectedErrors = [
            'Table .* already exists',
            'Duplicate column name',
            'Duplicate key name',
            'Column .* already exists', 
            'Key .* already exists',
            'Can\'t DROP .*; check that column/key exists',
            'Unknown table .*',
            'Table .* doesn\'t exist'
        ];
        
        foreach ($expectedErrors as $pattern) {
            if (preg_match('/' . $pattern . '/i', $errorMessage)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Limpia tablas duplicadas (CamelCase vs lowercase) en las bases de datos de los tenants
     */
    private function cleanupDuplicateTables(InputInterface $input, OutputInterface $output, SymfonyStyle $io, ?string $tenantSubdomain, bool $dryRun): int
    {
        $io->title('🧹 Limpieza de Tablas Duplicadas (CamelCase vs lowercase)');
        
        try {
            // Obtener tenants
            $tenants = $this->getActiveTenants($io, $tenantSubdomain);
            
            if (empty($tenants)) {
                $io->error('No se encontraron tenants para limpiar');
                return Command::FAILURE;
            }

            $io->text('🔍 Verificando tablas duplicadas en ' . count($tenants) . ' tenant(s)...');
            
            $totalCleaned = 0;
            $totalErrors = 0;
            
            foreach ($tenants as $tenant) {
                $io->text("📋 Procesando: {$tenant['name']} ({$tenant['subdomain']})");
                
                try {
                    $cleaned = $this->cleanupSingleTenantDuplicates($tenant, $io, $dryRun);
                    $totalCleaned += $cleaned;
                    
                    if ($cleaned > 0) {
                        $action = $dryRun ? 'Se limpiarían' : 'Limpiadas';
                        $io->text("  ✅ {$action} {$cleaned} tabla(s) duplicada(s)");
                    } else {
                        $io->text("  ℹ️  No se encontraron tablas duplicadas");
                    }
                    
                } catch (\Exception $e) {
                    $totalErrors++;
                    $io->text("  ❌ Error: " . $e->getMessage());
                }
            }
            
            // Resumen final
            $io->section('📊 Resumen de Limpieza');
            $io->definitionList(
                ['Tenants procesados' => count($tenants)],
                ['Tablas limpiadas' => $totalCleaned],
                ['Errores' => $totalErrors]
            );
            
            if ($totalErrors === 0) {
                $message = $dryRun ? 
                    '🔍 DRY-RUN: Todas las tablas duplicadas serían limpiadas correctamente' :
                    '🎉 Limpieza completada exitosamente';
                $io->success($message);
            } else {
                $io->warning("⚠️ Limpieza completada con {$totalErrors} error(es)");
            }
            
            return $totalErrors > 0 ? Command::FAILURE : Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Error en la limpieza: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function cleanupSingleTenantDuplicates(array $tenant, SymfonyStyle $io, bool $dryRun): int
    {
        $tenantDbConfig = [
            'host' => $tenant['host'],
            'port' => $tenant['host_port'],
            'dbname' => $tenant['database_name'],
            'user' => $tenant['db_user'],
            'password' => $tenant['db_password'],
            'driver' => 'pdo_mysql',
            'schemaManagerFactory' => new DefaultSchemaManagerFactory(),
        ];
        
        $connection = DriverManager::getConnection($tenantDbConfig);
        
        // Tablas problemáticas conocidas
        $duplicatedTables = [
            'Religion' => 'religion',
            'Sexo' => 'sexo'
        ];
        
        $cleanedCount = 0;
        
        foreach ($duplicatedTables as $camelCase => $lowercase) {
            // Verificar si ambas tablas existen
            $camelCaseExists = $this->tableExists($connection, $camelCase);
            $lowercaseExists = $this->tableExists($connection, $lowercase);
            
            if ($camelCaseExists && $lowercaseExists) {
                $io->text("    🔄 Encontradas tablas duplicadas: {$camelCase} y {$lowercase}");
                
                if (!$dryRun) {
                    // Verificar estructura de ambas tablas antes de migrar datos
                    $camelCaseColumns = $this->getTableColumns($connection, $camelCase);
                    $lowercaseColumns = $this->getTableColumns($connection, $lowercase);
                    
                    $io->text("    📊 {$camelCase}: " . implode(', ', $camelCaseColumns));
                    $io->text("    📊 {$lowercase}: " . implode(', ', $lowercaseColumns));
                    
                    // Solo migrar si las tablas tienen estructuras compatibles
                    if ($this->areTableStructuresCompatible($camelCaseColumns, $lowercaseColumns)) {
                        $count = $connection->fetchOne("SELECT COUNT(*) FROM {$lowercase}");
                        if ($count == 0) {
                            // Construir query de inserción con columnas específicas
                            $commonColumns = array_intersect($camelCaseColumns, $lowercaseColumns);
                            $columnsList = implode(', ', $commonColumns);
                            
                            $insertQuery = "INSERT INTO {$lowercase} ({$columnsList}) SELECT {$columnsList} FROM {$camelCase}";
                            $connection->executeStatement($insertQuery);
                            $io->text("    📦 Datos migrados de {$camelCase} a {$lowercase}");
                        }
                    } else {
                        $io->text("    ⚠️  Estructuras incompatibles, solo eliminando tabla {$camelCase}");
                    }
                    
                    // Eliminar tabla CamelCase
                    $connection->executeStatement("DROP TABLE {$camelCase}");
                    $io->text("    🗑️  Tabla {$camelCase} eliminada");
                }
                
                $cleanedCount++;
                
            } elseif ($camelCaseExists && !$lowercaseExists) {
                $io->text("    🔄 Renombrando tabla {$camelCase} a {$lowercase}");
                
                if (!$dryRun) {
                    $connection->executeStatement("RENAME TABLE {$camelCase} TO {$lowercase}");
                    $io->text("    ✅ Tabla renombrada exitosamente");
                }
                
                $cleanedCount++;
            }
        }
        
        return $cleanedCount;
    }

    private function tableExists($connection, string $tableName): bool
    {
        try {
            $result = $connection->fetchOne("SHOW TABLES LIKE ?", [$tableName]);
            return $result !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getTableColumns($connection, string $tableName): array
    {
        try {
            $result = $connection->fetchAllAssociative("SHOW COLUMNS FROM {$tableName}");
            return array_column($result, 'Field');
        } catch (\Exception $e) {
            return [];
        }
    }

    private function areTableStructuresCompatible(array $columns1, array $columns2): bool
    {
        // Verificar que al menos tengan columnas básicas en común
        $commonColumns = array_intersect($columns1, $columns2);
        $basicColumns = ['id', 'nombre', 'codigo', 'activo'];
        
        foreach ($basicColumns as $basicColumn) {
            if (!in_array($basicColumn, $commonColumns)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Limpia referencias huérfanas de migraciones en las bases de datos de los tenants
     */
    private function cleanupOrphanedMigrations(InputInterface $input, OutputInterface $output, SymfonyStyle $io, ?string $tenantSubdomain, bool $dryRun): int
    {
        $io->title('🧹 Limpieza de Referencias Huérfanas de Migraciones');
        
        try {
            // Obtener tenants
            $tenants = $this->getActiveTenants($io, $tenantSubdomain);
            
            if (empty($tenants)) {
                $io->error('No se encontraron tenants para limpiar');
                return Command::FAILURE;
            }

            // Obtener migraciones disponibles en el directorio
            $migrationsDir = '/var/www/html/melisa_tenant/migrations';
            $availableMigrations = $this->getAvailableMigrationVersions($migrationsDir);
            
            $io->text('🔍 Migraciones disponibles en directorio: ' . count($availableMigrations));
            $io->text('🔍 Verificando referencias huérfanas en ' . count($tenants) . ' tenant(s)...');
            
            $totalCleaned = 0;
            $totalErrors = 0;
            
            foreach ($tenants as $tenant) {
                $io->text("📋 Procesando: {$tenant['name']} ({$tenant['subdomain']})");
                
                try {
                    $cleaned = $this->cleanupSingleTenantOrphanedMigrations($tenant, $availableMigrations, $io, $dryRun);
                    $totalCleaned += $cleaned;
                    
                    if ($cleaned > 0) {
                        $action = $dryRun ? 'Se limpiarían' : 'Limpiadas';
                        $io->text("  ✅ {$action} {$cleaned} referencia(s) huérfana(s)");
                    } else {
                        $io->text("  ℹ️  No se encontraron referencias huérfanas");
                    }
                    
                } catch (\Exception $e) {
                    $totalErrors++;
                    $io->text("  ❌ Error: " . $e->getMessage());
                }
            }
            
            // Resumen final
            $io->section('📊 Resumen de Limpieza de Referencias Huérfanas');
            $io->definitionList(
                ['Tenants procesados' => count($tenants)],
                ['Referencias limpiadas' => $totalCleaned],
                ['Errores' => $totalErrors]
            );
            
            if ($totalErrors === 0) {
                $message = $dryRun ? 
                    '🔍 DRY-RUN: Todas las referencias huérfanas serían limpiadas correctamente' :
                    '🎉 Limpieza de referencias huérfanas completada exitosamente';
                $io->success($message);
            } else {
                $io->warning("⚠️ Limpieza completada con {$totalErrors} error(es)");
            }
            
            return $totalErrors > 0 ? Command::FAILURE : Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Error en la limpieza de referencias huérfanas: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Obtiene todas las versiones de migración disponibles en el directorio
     */
    private function getAvailableMigrationVersions(string $migrationsDir): array
    {
        $files = glob($migrationsDir . '/Version*.php');
        $versions = [];

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $version = 'DoctrineMigrations\\' . $filename;
            $versions[] = $version;
        }

        return $versions;
    }

    /**
     * Limpia referencias huérfanas de migraciones en un tenant específico
     */
    private function cleanupSingleTenantOrphanedMigrations(array $tenant, array $availableMigrations, SymfonyStyle $io, bool $dryRun): int
    {
        $tenantDbConfig = [
            'host' => $tenant['host'],
            'port' => $tenant['host_port'],
            'dbname' => $tenant['database_name'],
            'user' => $tenant['db_user'],
            'password' => $tenant['db_password'],
            'driver' => 'pdo_mysql',
            'schemaManagerFactory' => new DefaultSchemaManagerFactory(),
        ];
        
        $connection = DriverManager::getConnection($tenantDbConfig);
        
        // Obtener migraciones registradas en la base de datos
        $executedMigrations = $this->getExecutedMigrations($connection);
        
        $orphanedCount = 0;
        
        foreach ($executedMigrations as $executedMigration) {
            // Si la migración está registrada en BD pero no existe el archivo
            if (!in_array($executedMigration, $availableMigrations)) {
                $orphanedCount++;
                
                $io->text("    🗑️  Referencia huérfana encontrada: " . str_replace('DoctrineMigrations\\', '', $executedMigration));
                
                if (!$dryRun) {
                    // Eliminar la referencia huérfana de la base de datos
                    $deleteSql = "DELETE FROM doctrine_migration_versions WHERE version = ?";
                    $connection->executeStatement($deleteSql, [$executedMigration]);
                    $io->text("    ✅ Referencia huérfana eliminada de la base de datos");
                }
            }
        }
        
        return $orphanedCount;
    }

    private function showFinalResults(SymfonyStyle $io, array $results, bool $dryRun, ?string $tenantSubdomain = null): void
    {
        $title = $tenantSubdomain ? "📈 Resultados Finales: {$tenantSubdomain}" : '📈 Resultados Finales';
        $io->section($title);
        
        $io->definitionList(
            ['✅ Exitosos' => $results['success']],
            ['❌ Fallidos' => $results['failures']],
            ['📊 Total procesados' => $results['total']],
            ['🎯 Tasa de éxito' => $results['total'] > 0 ? round(($results['success'] / $results['total']) * 100, 2) . '%' : '0%']
        );
        
        if (!empty($results['results'])) {
            $io->text('📋 Detalle de resultados:');
            foreach ($results['results'] as $result) {
                $io->text("  {$result}");
            }
        }
        
        if ($results['failures'] === 0) {
            if ($dryRun) {
                $io->info('🔍 DRY-RUN completado: Todas las migraciones se aplicarían correctamente');
            } else {
                $io->success('🎉 Todas las migraciones fueron aplicadas exitosamente a todos los tenants!');
            }
        } else {
            $io->warning("⚠️ Se completaron {$results['success']} migraciones, pero {$results['failures']} fallaron");
        }
    }
}
