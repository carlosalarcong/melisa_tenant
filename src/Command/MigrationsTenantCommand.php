<?php

namespace App\Command;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:migrations-tenant',
    description: 'Generar migraciones basadas en entidades de src/Entity usando una base de datos tenant como referencia'
)]
class MigrationsTenantCommand extends Command
{
    private array $centralDbConfig;

    public function __construct(string $centralDbUrl)
    {
        parent::__construct();
        
        // Parse DATABASE_URL to connect to central DB for tenant list
        $urlParts = parse_url($centralDbUrl);
        $this->centralDbConfig = [
            'host' => $urlParts['host'] ?? 'localhost',
            'port' => $urlParts['port'] ?? 3306,
            'dbname' => trim($urlParts['path'] ?? '/melisa_central', '/'),
            'user' => $urlParts['user'] ?? 'melisa',
            'password' => $urlParts['pass'] ?? 'melisamelisa',
            'driver' => 'pdo_mysql',
        ];
    }

    protected function configure(): void
    {
        $this
            ->addArgument('tenant', InputArgument::REQUIRED, 'Subdomain del tenant a usar como referencia (ej: melisalacolina, melisahospital)')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Solo mostrar qué se ejecutaría sin hacer cambios')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Forzar ejecución sin confirmación')
            ->setHelp('
Este comando genera migraciones comparando las entidades de src/Entity/ 
con el esquema actual de un tenant ESPECÍFICO.

<info>Flujo de trabajo:</info>

1. <comment>Creas/modificas entidades</comment> en src/Entity/
   Ejemplo: src/Entity/Paciente.php

2. <comment>Generas migraciones usando un tenant específico</comment>
   php bin/console app:migrations-tenant melisalacolina
   
   - Usa la BD del tenant especificado como referencia
   - Compara entidades (src/Entity/) vs esquema actual de esa BD
   - Genera migrations/Version*.php solo si hay diferencias
   
3. <comment>Aplicas las migraciones a TODOS los tenants</comment>
   php bin/console app:migrate-tenant

<info>Ejemplos de uso:</info>

  <comment># Generar migraciones usando melisalacolina como referencia</comment>
  php bin/console app:migrations-tenant melisalacolina

  <comment># Generar migraciones usando melisahospital como referencia</comment>
  php bin/console app:migrations-tenant melisahospital

  <comment># Verificar qué se haría sin generar (dry-run)</comment>
  php bin/console app:migrations-tenant melisalacolina --dry-run

<info>Importante:</info>
- El tenant especificado debe tener las migraciones actuales aplicadas
- Si el tenant no está al día, primero ejecuta: php bin/console app:migrate-tenant
- Las migraciones generadas se aplicarán a TODOS los tenants
            ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $force = $input->getOption('force');
        $tenantSubdomain = $input->getArgument('tenant');

        $io->title("🔧 Generación de Migraciones usando tenant: {$tenantSubdomain}");
        
        try {
            // 1. Obtener el tenant especificado
            $tenantSubdomain = $input->getArgument('tenant');
            $tenant = $this->getTenantBySubdomain($io, $tenantSubdomain);
            
            if (!$tenant) {
                $io->error("No se encontró el tenant '{$tenantSubdomain}' o no está activo");
                return Command::FAILURE;
            }

            // 2. Mostrar información
            $this->showGenerationInfo($io, $tenant, $dryRun);

            // 3. Configurar conexión temporal a la BD del tenant
            $tenantDbUrl = $this->buildTenantDatabaseUrl($tenant);
            
            if ($dryRun) {
                $io->info('🔍 DRY-RUN: Se usaría la BD del tenant para generar migraciones');
                $io->text("   BD de referencia: {$tenant['database_name']}");
                return Command::SUCCESS;
            }

            // 4. Generar migraciones usando la BD del tenant como referencia
            $this->generateMigrationsWithTenantDb($tenantDbUrl, $tenant, $io, $force);

            // 5. Resultado final
            $io->success('✅ Proceso completado!');
            $io->text('� Las migraciones se encuentran en: ./migrations/');
            $io->text('');
            $io->text('💡 Siguiente paso: Aplicar migraciones a todos los tenants');
            $io->text('   <comment>php bin/console app:migrate-tenant</comment>');

            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Error generando migraciones: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function getTenantBySubdomain(SymfonyStyle $io, string $tenantSubdomain): ?array
    {
        $io->section("🔍 Obteniendo Tenant: {$tenantSubdomain}");
        
        try {
            $config = $this->centralDbConfig;
            $config['schemaManagerFactory'] = new DefaultSchemaManagerFactory();
            $connection = DriverManager::getConnection($config);
            
            $query = "
                SELECT id, name, subdomain, database_name,
                       COALESCE(host, 'localhost') as host,
                       COALESCE(host_port, 3306) as host_port,
                       COALESCE(db_user, 'melisa') as db_user,
                       COALESCE(db_password, 'melisamelisa') as db_password
                FROM tenant 
                WHERE subdomain = ? AND is_active = 1
                LIMIT 1
            ";
            $result = $connection->executeQuery($query, [$tenantSubdomain]);
            $tenant = $result->fetchAssociative();
            
            if ($tenant) {
                $io->text("✅ Tenant encontrado: {$tenant['name']} ({$tenant['subdomain']})");
                $io->text("   Base de datos: {$tenant['database_name']}");
            } else {
                $io->error("No se encontró el tenant: {$tenantSubdomain}");
            }
            
            return $tenant ?: null;
            
        } catch (\Exception $e) {
            $io->error('Error obteniendo tenant: ' . $e->getMessage());
            return null;
        }
    }

    private function getReferenceTenant(SymfonyStyle $io): ?array
    {
        $io->section('🔍 Obteniendo Tenant de Referencia');
        
        try {
            $connection = DriverManager::getConnection($this->centralDbConfig);
            
            $query = "
                SELECT id, name, subdomain, database_name,
                       COALESCE(host, 'localhost') as host,
                       COALESCE(host_port, 3306) as host_port,
                       COALESCE(db_user, 'melisa') as db_user,
                       COALESCE(db_password, 'melisamelisa') as db_password
                FROM tenant 
                WHERE is_active = 1
                ORDER BY id
                LIMIT 1
            ";
            $result = $connection->executeQuery($query);
            $tenant = $result->fetchAssociative();
            
            if ($tenant) {
                $io->text("✅ Usando tenant de referencia: {$tenant['name']} ({$tenant['subdomain']})");
                $io->text("   Base de datos: {$tenant['database_name']}");
                $io->note('El tenant de referencia solo se usa para comparar el esquema actual. Las migraciones se aplicarán a TODOS los tenants.');
            } else {
                $io->error("No se encontraron tenants activos");
            }
            
            return $tenant ?: null;
            
        } catch (\Exception $e) {
            $io->error('Error obteniendo tenant: ' . $e->getMessage());
            return null;
        }
    }

    private function showGenerationInfo(SymfonyStyle $io, array $tenant, bool $dryRun): void
    {
        $io->section('📊 Información de Generación');
        
        $mode = $dryRun ? '🔍 DRY-RUN (simulación)' : '🔄 GENERACIÓN REAL';
        
        $io->definitionList(
            ['Modo' => $mode],
            ['Tenant de referencia' => "{$tenant['name']} ({$tenant['subdomain']})"],
            ['Base de datos' => $tenant['database_name']],
            ['Directorio entidades' => './src/Entity/'],
            ['Entidades detectadas' => $this->countEntities()],
            ['Directorio migraciones' => './migrations/']
        );
        
        $io->note('Las migraciones se generan comparando las entidades con el esquema actual de la BD del tenant.');
    }

    private function countEntities(): int
    {
        $entityDir = '/var/www/html/melisa_tenant/src/Entity';
        if (!is_dir($entityDir)) {
            return 0;
        }
        
        $entities = glob($entityDir . '/*.php');
        $entities = array_filter($entities, fn($file) => pathinfo($file, PATHINFO_EXTENSION) === 'php');
        return count($entities);
    }

    private function buildTenantDatabaseUrl(array $tenant): string
    {
        return sprintf(
            'mysql://%s:%s@%s:%d/%s',
            $tenant['db_user'],
            $tenant['db_password'],
            $tenant['host'],
            $tenant['host_port'],
            $tenant['database_name']
        );
    }

    private function generateMigrationsWithTenantDb(string $tenantDbUrl, array $tenant, SymfonyStyle $io, bool $force = false): void
    {
        $io->section('📦 Generando Migraciones');
        
        try {
            // Primero verificar con doctrine:migrations:status si hay migraciones pendientes
            $io->text("🔍 Verificando estado de migraciones en BD de referencia...");
            
            $statusProcess = new Process([
                'php', 'bin/console', 'doctrine:migrations:up-to-date', '--no-interaction'
            ]);
            
            $statusProcess->setEnv(['DATABASE_URL' => $tenantDbUrl]);
            $statusProcess->setWorkingDirectory('/var/www/html/melisa_tenant');
            $statusProcess->setTimeout(60);
            $statusProcess->run();
            
            $statusOutput = $statusProcess->getOutput();
            $isUpToDate = $statusProcess->getExitCode() === 0;
            
            if ($isUpToDate) {
                $io->text("✅ La BD de referencia está actualizada con todas las migraciones");
                $io->newLine();
            } else {
                $io->warning("⚠️  La BD de referencia '{$tenant['database_name']}' tiene migraciones pendientes");
                $io->text("   Recomendación: Ejecuta primero 'php bin/console app:migrate-tenant'");
                $io->newLine();
                
                if (!$force && !$io->confirm('¿Deseas continuar de todos modos?', false)) {
                    $io->note('Operación cancelada. Aplica las migraciones pendientes primero.');
                    return;
                }
            }
            
            // Ahora verificar si hay cambios en las entidades vs el esquema actual
            $io->text("🔍 Comparando entidades con esquema actual...");
            
            $validateProcess = new Process([
                'php', 'bin/console', 'doctrine:schema:update', '--dump-sql', '--no-interaction'
            ]);
            
            $validateProcess->setEnv(['DATABASE_URL' => $tenantDbUrl]);
            $validateProcess->setWorkingDirectory('/var/www/html/melisa_tenant');
            $validateProcess->setTimeout(60);
            $validateProcess->run();
            
            $validateOutput = $validateProcess->getOutput();
            
            // Limpiar output
            $cleanOutput = trim($validateOutput);
            $cleanOutput = preg_replace('/\[.*?\]/', '', $cleanOutput); // Quitar timestamps
            $cleanOutput = trim($cleanOutput);
            
            // Si no hay cambios reales, no generar migración
            if (empty($cleanOutput) || 
                strpos($validateOutput, 'Nothing to update') !== false || 
                strpos($validateOutput, 'No changes detected') !== false ||
                strpos($cleanOutput, 'ALTER TABLE') === 0 && strpos($cleanOutput, 'CHANGE') !== false && str_word_count($cleanOutput) < 30) {
                
                $io->success('✅ El esquema ya está sincronizado con las entidades');
                $io->text('   No se detectaron cambios que requieran nueva migración.');
                $io->newLine();
                $io->text('💡 Si modificaste entidades, asegúrate de:');
                $io->text('   1. Guardar los cambios en los archivos .php');
                $io->text('   2. Limpiar cache: php bin/console cache:clear');
                return;
            }
            
            // Mostrar cambios detectados
            $io->text("📋 Cambios detectados:");
            $sqlLines = explode("\n", $cleanOutput);
            $relevantChanges = 0;
            
            foreach ($sqlLines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '[') === 0) continue;
                
                // Filtrar cambios triviales de COMMENT
                if (strpos($line, 'CHANGE') !== false && 
                    strpos($line, 'COMMENT') !== false && 
                    !preg_match('/(ADD|DROP|MODIFY|VARCHAR|INT|TEXT|DATE)/i', $line)) {
                    continue;
                }
                
                $io->text("   → " . substr($line, 0, 120));
                $relevantChanges++;
            }
            
            if ($relevantChanges === 0) {
                $io->success('✅ Solo se detectaron cambios triviales (comentarios), no se requiere migración');
                return;
            }
            
            $io->newLine();
            
            // Ejecutar doctrine:migrations:diff
            $process = new Process([
                'php', 'bin/console', 'doctrine:migrations:diff', '--no-interaction', '--allow-empty-diff'
            ]);
            
            $process->setEnv(['DATABASE_URL' => $tenantDbUrl]);
            $process->setWorkingDirectory('/var/www/html/melisa_tenant');
            $process->setTimeout(120);
            
            $io->text("🔄 Generando archivo de migración...");
            
            $process->run();
            
            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                
                if (strpos($output, 'Generated new migration') !== false) {
                    $io->newLine();
                    $io->success('✅ Migración generada exitosamente!');
                    
                    // Extraer nombre del archivo generado
                    if (preg_match('/migrations\/(Version\d+\.php)/', $output, $matches)) {
                        $migrationFile = './migrations/' . $matches[1];
                        $io->text("📄 Archivo: {$migrationFile}");
                        $io->newLine();
                        $io->text('⚠️  IMPORTANTE: Revisa el archivo generado y:');
                        $io->text('   1. Elimina líneas de DROP TABLE si no son necesarias');
                        $io->text('   2. Verifica que los cambios sean correctos');
                        $io->text('   3. Elimina cambios triviales (COMMENT, etc.)');
                    }
                } elseif (strpos($output, 'no changes') !== false || 
                          strpos($output, 'up to date') !== false ||
                          strpos($output, 'no migration') !== false) {
                    $io->info('ℹ️  No se generó migración (esquema ya sincronizado)');
                } else {
                    $io->text('ℹ️  ' . trim($output));
                }
            } else {
                $error = $process->getErrorOutput();
                
                if (strpos($error, 'no changes') !== false || strpos($error, 'up-to-date') !== false) {
                    $io->info('ℹ️  No hay cambios que requieran nueva migración');
                } else {
                    throw new \Exception('Error ejecutando doctrine:migrations:diff: ' . $error);
                }
            }
            
        } catch (\Exception $e) {
            throw new \Exception('Error generando migraciones: ' . $e->getMessage());
        }
    }
}
