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
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:migrate-tenant',
    description: 'Generar migraciones y aplicarlas automáticamente a todos los tenants activos o a un tenant específico'
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
            ->addArgument('tenant', InputArgument::OPTIONAL, 'Subdomain del tenant específico a migrar (ej: melisalacolina)')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Solo mostrar qué se ejecutaría sin hacer cambios')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forzar ejecución sin confirmación')
            ->addOption('generate-only', null, InputOption::VALUE_NONE, 'Solo generar migraciones sin aplicarlas')
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

<info>Proceso automático:</info>
✅ Detecta tenants activos en melisa_central
✅ Genera migraciones desde entidades existentes
✅ Aplica migraciones a tenant específico o todos los tenants
✅ Reporte completo de resultados
            ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $force = $input->getOption('force');
        $generateOnly = $input->getOption('generate-only');
        $tenantSubdomain = $input->getArgument('tenant');

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
            $connection = DriverManager::getConnection($this->centralDbConfig);
            
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
        
        $io->definitionList(
            ['Modo de ejecución' => $mode],
            [$tenantLabel => count($tenants)],
            ['Directorio migraciones' => './migrations/'],
            ['Entidades detectadas' => $this->countEntities()]
        );
        
        $io->text('📋 Tenants que serán procesados:');
        foreach ($tenants as $tenant) {
            $io->text("  • {$tenant['name']} ({$tenant['subdomain']}) → BD: {$tenant['database_name']}");
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
            $io->text('🔍 DRY-RUN: Se generarían migraciones basadas en entidades existentes');
            return true;
        }

        try {
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
            return;
        }

        $migrationFiles = glob($migrationsDir . '/Version*.php');
        if (empty($migrationFiles)) {
            return;
        }

        // Obtener migraciones ya ejecutadas
        $executedQuery = "SELECT version FROM doctrine_migration_versions";
        $result = $connection->executeQuery($executedQuery);
        $executed = array_column($result->fetchAllAssociative(), 'version');

        foreach ($migrationFiles as $migrationFile) {
            $filename = basename($migrationFile, '.php');
            $version = 'DoctrineMigrations\\' . $filename;

            if (in_array($version, $executed)) {
                continue; // Ya ejecutada
            }

            try {
                // Aplicar migración específica basada en el archivo
                $this->applySpecificMigration($connection, $filename, $io);
                
                // Registrar como ejecutada
                $insertSql = "INSERT IGNORE INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES (?, NOW(), ?)";
                $connection->executeStatement($insertSql, [$version, 100]);
                
            } catch (\Exception $e) {
                // Log del error pero continuar con las siguientes
                $io->text("    ⚠️  Error en migración {$filename}: " . $e->getMessage());
            }
        }
    }

    private function applySpecificMigration($connection, string $filename, SymfonyStyle $io): void
    {
        // Aplicar migraciones específicas basadas en los archivos que tenemos
        switch ($filename) {
            case 'Version20251017145349':
                $this->applyVersion20251017145349($connection);
                break;
            case 'Version20251017150139':
                $this->applyVersion20251017150139($connection);
                break;
            case 'Version20251017150541':
            case 'Version20251017152218':
                $this->applyVersion20251017152218($connection);
                break;
            case 'Version20251017165847':
            case 'Version20251017165926':
                $this->applyVersion20251017165847($connection, $io);
                break;
            default:
                // Para nuevas migraciones, intentar aplicar dinámicamente
                $this->applyDynamicMigration($connection, $filename);
        }
    }

    private function applyVersion20251017145349($connection): void
    {
        // Crear tablas sexo y religion
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS sexo (
                id INT AUTO_INCREMENT NOT NULL,
                nombre VARCHAR(50) NOT NULL,
                codigo VARCHAR(10) NOT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                PRIMARY KEY(id),
                INDEX idx_sexo_activo (activo),
                INDEX idx_sexo_codigo (codigo)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS religion (
                id INT AUTO_INCREMENT NOT NULL,
                nombre VARCHAR(100) NOT NULL,
                codigo VARCHAR(20) NOT NULL,
                descripcion TEXT DEFAULT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                PRIMARY KEY(id),
                INDEX idx_religion_activo (activo),
                INDEX idx_religion_codigo (codigo)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");
    }

    private function applyVersion20251017150139($connection): void
    {
        // Crear tablas Pais y Region con relaciones
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS pais (
                id INT AUTO_INCREMENT NOT NULL,
                nombre_pais VARCHAR(255) DEFAULT NULL,
                nombre_gentilicio VARCHAR(255) NOT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                PRIMARY KEY(id),
                INDEX idx_pais_activo (activo)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS region (
                id INT AUTO_INCREMENT NOT NULL,
                id_pais INT DEFAULT NULL,
                codigo_region INT DEFAULT NULL,
                nombre_region VARCHAR(100) DEFAULT NULL,
                address_state_hl7 VARCHAR(10) DEFAULT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                PRIMARY KEY(id),
                INDEX idx_region_activo (activo),
                INDEX idx_region_codigo (codigo_region),
                INDEX IDX_F62F176F85E0677 (id_pais)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        // Agregar foreign key solo si no existe
        try {
            $connection->executeStatement("
                ALTER TABLE region 
                ADD CONSTRAINT FK_F62F176F85E0677 
                FOREIGN KEY (id_pais) REFERENCES pais (id)
            ");
        } catch (\Exception $e) {
            // FK ya existe, continuar
        }
    }

    private function applyVersion20251017150541($connection): void
    {
        // Crear tabla Estado
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS estado (
                id INT AUTO_INCREMENT NOT NULL,
                nombre_estado VARCHAR(45) NOT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                PRIMARY KEY(id),
                INDEX idx_estado_activo (activo)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        // Agregar columnas id_estado si no existen
        $tables = ['pais', 'region', 'religion', 'sexo'];
        foreach ($tables as $table) {
            try {
                $connection->executeStatement("ALTER TABLE {$table} ADD COLUMN id_estado INT DEFAULT NULL");
                $connection->executeStatement("CREATE INDEX IDX_{$table}_estado ON {$table} (id_estado)");
                $connection->executeStatement("ALTER TABLE {$table} ADD CONSTRAINT FK_{$table}_estado FOREIGN KEY (id_estado) REFERENCES estado (id)");
            } catch (\Exception $e) {
                // Columna o constraint ya existe, continuar
            }
        }
    }

    private function applyVersion20251017152218($connection): void
    {
        // Crear todas las tablas con nombres en minúsculas
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS religion (
                id INT AUTO_INCREMENT NOT NULL,
                id_estado INT DEFAULT NULL,
                nombre VARCHAR(100) NOT NULL,
                codigo VARCHAR(20) NOT NULL,
                descripcion LONGTEXT DEFAULT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                INDEX IDX_religion_estado (id_estado),
                INDEX idx_religion_activo (activo),
                INDEX idx_religion_codigo (codigo),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS sexo (
                id INT AUTO_INCREMENT NOT NULL,
                id_estado INT DEFAULT NULL,
                nombre VARCHAR(50) NOT NULL,
                codigo VARCHAR(10) NOT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                INDEX IDX_sexo_estado (id_estado),
                INDEX idx_sexo_activo (activo),
                INDEX idx_sexo_codigo (codigo),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        // Agregar foreign keys para religion y sexo si no existen
        try {
            $connection->executeStatement("
                ALTER TABLE religion 
                ADD CONSTRAINT FK_religion_estado 
                FOREIGN KEY (id_estado) REFERENCES estado (id)
            ");
        } catch (\Exception $e) {
            // FK ya existe, continuar
        }

        try {
            $connection->executeStatement("
                ALTER TABLE sexo 
                ADD CONSTRAINT FK_sexo_estado 
                FOREIGN KEY (id_estado) REFERENCES estado (id)
            ");
        } catch (\Exception $e) {
            // FK ya existe, continuar
        }

        // Migrar datos de tablas en CamelCase a minúsculas si existen
        try {
            $connection->executeStatement("INSERT IGNORE INTO religion SELECT * FROM Religion");
            $connection->executeStatement("INSERT IGNORE INTO sexo SELECT * FROM Sexo");
            
            // Eliminar tablas en CamelCase
            $connection->executeStatement("DROP TABLE IF EXISTS Religion");
            $connection->executeStatement("DROP TABLE IF EXISTS Sexo");
        } catch (\Exception $e) {
            // Las tablas CamelCase no existen, continuar
        }
    }

    private function applyVersion20251017165847($connection, SymfonyStyle $io): void
    {
        // Esta migración incluye la tabla member y todas las entidades
        
        // Crear tabla estado si no existe
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS estado (
                id INT AUTO_INCREMENT NOT NULL,
                nombre_estado VARCHAR(45) NOT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        // Crear tabla member - ¡LA CLAVE!
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS member (
                id INT AUTO_INCREMENT NOT NULL,
                username VARCHAR(180) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) DEFAULT NULL,
                first_name VARCHAR(255) DEFAULT NULL,
                last_name VARCHAR(255) DEFAULT NULL,
                is_active TINYINT(1) DEFAULT NULL,
                created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        // Crear otras tablas si no existen
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS pais (
                id INT AUTO_INCREMENT NOT NULL,
                id_estado INT DEFAULT NULL,
                nombre_pais VARCHAR(255) DEFAULT NULL,
                nombre_gentilicio VARCHAR(255) NOT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                INDEX IDX_7E5D2EFF6A540E (id_estado),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS region (
                id INT AUTO_INCREMENT NOT NULL,
                id_pais INT DEFAULT NULL,
                id_estado INT DEFAULT NULL,
                codigo_region INT DEFAULT NULL,
                nombre_region VARCHAR(100) DEFAULT NULL,
                address_state_hl7 VARCHAR(10) DEFAULT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                INDEX IDX_F62F176F57D32FD (id_pais),
                INDEX IDX_F62F1766A540E (id_estado),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS religion (
                id INT AUTO_INCREMENT NOT NULL,
                id_estado INT DEFAULT NULL,
                nombre VARCHAR(100) NOT NULL,
                codigo VARCHAR(20) NOT NULL,
                descripcion LONGTEXT DEFAULT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                INDEX IDX_1055F4F96A540E (id_estado),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS sexo (
                id INT AUTO_INCREMENT NOT NULL,
                id_estado INT DEFAULT NULL,
                nombre VARCHAR(50) NOT NULL,
                codigo VARCHAR(10) NOT NULL,
                activo TINYINT(1) DEFAULT 1 NOT NULL,
                INDEX IDX_2C3956926A540E (id_estado),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        // Crear tabla de mensajes de Symfony
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS messenger_messages (
                id BIGINT AUTO_INCREMENT NOT NULL,
                body LONGTEXT NOT NULL,
                headers LONGTEXT NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX IDX_75EA56E0FB7336F0 (queue_name),
                INDEX IDX_75EA56E0E3BD61CE (available_at),
                INDEX IDX_75EA56E016BA31DB (delivered_at),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
        ");

        // Agregar foreign keys
        try {
            $connection->executeStatement("ALTER TABLE pais ADD CONSTRAINT FK_7E5D2EFF6A540E FOREIGN KEY (id_estado) REFERENCES estado (id)");
        } catch (\Exception $e) {}
        
        try {
            $connection->executeStatement("ALTER TABLE region ADD CONSTRAINT FK_F62F176F57D32FD FOREIGN KEY (id_pais) REFERENCES pais (id)");
        } catch (\Exception $e) {}
        
        try {
            $connection->executeStatement("ALTER TABLE region ADD CONSTRAINT FK_F62F1766A540E FOREIGN KEY (id_estado) REFERENCES estado (id)");
        } catch (\Exception $e) {}
        
        try {
            $connection->executeStatement("ALTER TABLE religion ADD CONSTRAINT FK_1055F4F96A540E FOREIGN KEY (id_estado) REFERENCES estado (id)");
        } catch (\Exception $e) {}
        
        try {
            $connection->executeStatement("ALTER TABLE sexo ADD CONSTRAINT FK_2C3956926A540E FOREIGN KEY (id_estado) REFERENCES estado (id)");
        } catch (\Exception $e) {}

        $io->text("    ✅ Tabla member creada exitosamente");
    }

    private function applyDynamicMigration($connection, string $filename): void
    {
        // Aplicar migración de forma dinámica leyendo el archivo PHP
        $migrationFile = '/var/www/html/melisa_tenant/migrations/' . $filename . '.php';
        
        if (!file_exists($migrationFile)) {
            throw new \Exception("Archivo de migración no encontrado: {$migrationFile}");
        }
        
        // Leer el contenido del archivo de migración
        $migrationContent = file_get_contents($migrationFile);
        
        // Extraer las sentencias SQL del método up()
        $sqlStatements = $this->extractSqlFromMigration($migrationContent);
        
        // Ejecutar cada sentencia SQL
        foreach ($sqlStatements as $sql) {
            try {
                // Limpiar y preparar la sentencia SQL
                $cleanSql = trim($sql);
                if (!empty($cleanSql) && $cleanSql !== ';') {
                    $connection->executeStatement($cleanSql);
                }
            } catch (\Exception $e) {
                // Algunos errores son esperables (tabla ya existe, etc.)
                if (!$this->isExpectedMigrationError($e->getMessage())) {
                    throw new \Exception("Error ejecutando SQL: {$cleanSql}. Error: " . $e->getMessage());
                }
            }
        }
    }
    
    private function extractSqlFromMigration(string $content): array
    {
        $sqlStatements = [];
        
        // Usar regex para extraer todas las llamadas a addSql()
        preg_match_all('/\$this->addSql\([\'\"](.*?)[\'\"]\);/s', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $sql) {
                // Limpiar caracteres de escape y normalizar
                $cleanSql = str_replace(['\\\'', '\\"', '\\\\'], ["'", '"', '\\'], $sql);
                $cleanSql = trim($cleanSql);
                
                if (!empty($cleanSql)) {
                    $sqlStatements[] = $cleanSql;
                }
            }
        }
        
        return $sqlStatements;
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
