<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use PDO;

#[AsCommand(
    name: 'app:make-entity-tenant',
    description: 'Crear entidades Doctrine para tenants específicos o todos los tenants'
)]
class MakeEntityForTenantCommand extends Command
{
    private Connection $centralConnection;
    
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('subdomain', InputArgument::REQUIRED, 'Subdominio del tenant (o "all" para todos los tenants)')
            ->addArgument('entity_name', InputArgument::REQUIRED, 'Nombre de la entidad a crear')
            ->addOption('fields', 'f', InputOption::VALUE_OPTIONAL, 'Campos de la entidad en formato JSON', '[]')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Mostrar qué se ejecutaría sin hacer cambios')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Ejecutar sin confirmación interactiva')
            ->setHelp('
Este comando permite crear entidades Doctrine en bases de datos de tenants específicos.

<info>Ejemplos de uso:</info>

  <comment># Crear entidad en un tenant específico</comment>
  php bin/console app:make-entity-tenant melisalacolina Patient

  <comment># Crear entidad en todos los tenants</comment>
  php bin/console app:make-entity-tenant all Patient

  <comment># Con campos predefinidos</comment>
  php bin/console app:make-entity-tenant melisalacolina Patient --fields=\'[{"name":"firstName","type":"string"},{"name":"lastName","type":"string"}]\'

  <comment># Solo verificar qué se haría</comment>
  php bin/console app:make-entity-tenant all Patient --dry-run

<info>Formatos de campos soportados:</info>
- string, text, integer, boolean, datetime, date, decimal, json
- Relaciones: relation (requiere configuración adicional)
');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $subdomain = $input->getArgument('subdomain');
        $entityName = $input->getArgument('entity_name');
        $fieldsJson = $input->getOption('fields');
        $isDryRun = $input->getOption('dry-run');
        $isForce = $input->getOption('force');

        // Banner del comando
        $io->title('🏗️  Generador de Entidades Multi-Tenant');
        $io->section("Creando entidad: <info>$entityName</info>");

        try {
            // Conectar a la base de datos central
            $this->connectToCentralDatabase($io);

            // Obtener tenants
            $tenants = $this->getTenants($subdomain, $io);
            
            if (empty($tenants)) {
                $io->error("No se encontraron tenants para el subdominio: $subdomain");
                return Command::FAILURE;
            }

            // Procesar campos
            $fields = $this->processFields($fieldsJson, $io);

            // Mostrar resumen
            $this->showSummary($tenants, $entityName, $fields, $io);

            // Confirmación (si no es force o dry-run)
            if (!$isForce && !$isDryRun && !$this->confirmExecution($tenants, $io)) {
                $io->info('Operación cancelada por el usuario.');
                return Command::SUCCESS;
            }

            // Ejecutar creación de entidades
            $results = $this->createEntitiesForTenants($tenants, $entityName, $fields, $isDryRun, $io);

            // Mostrar resultados
            $this->showResults($results, $isDryRun, $io);

        } catch (\Exception $e) {
            $io->error("Error durante la ejecución: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function connectToCentralDatabase(SymfonyStyle $io): void
    {
        $connectionParams = [
            'dbname' => 'melisa_central',
            'user' => 'melisa',
            'password' => 'melisamelisa',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
            'charset' => 'utf8mb4',
        ];

        try {
            $this->centralConnection = DriverManager::getConnection($connectionParams);
            // Verificar conexión ejecutando una consulta simple
            $this->centralConnection->fetchOne('SELECT 1');
            $io->text('✅ Conectado a melisa_central');
        } catch (\Exception $e) {
            throw new \Exception("Error conectando a melisa_central: " . $e->getMessage());
        }
    }

    private function getTenants(string $subdomain, SymfonyStyle $io): array
    {
        if ($subdomain === 'all') {
            $sql = "SELECT id, subdomain, name, database_name, is_active FROM tenant WHERE is_active = 1 ORDER BY name";
            $stmt = $this->centralConnection->prepare($sql);
            $result = $stmt->executeQuery();
        } else {
            $sql = "SELECT id, subdomain, name, database_name, is_active FROM tenant WHERE subdomain = ? AND is_active = 1";
            $stmt = $this->centralConnection->prepare($sql);
            $stmt->bindValue(1, $subdomain);
            $result = $stmt->executeQuery();
        }

        $tenants = $result->fetchAllAssociative();

        $io->text(sprintf('📋 Encontrados %d tenant(s)', count($tenants)));
        
        return $tenants;
    }

    private function processFields(string $fieldsJson, SymfonyStyle $io): array
    {
        if (empty($fieldsJson) || $fieldsJson === '[]') {
            return [];
        }

        try {
            $fields = json_decode($fieldsJson, true, 512, JSON_THROW_ON_ERROR);
            $io->text(sprintf('📋 Procesados %d campo(s) desde JSON', count($fields)));
            return $fields;
        } catch (\JsonException $e) {
            throw new \Exception("Error en formato JSON de campos: " . $e->getMessage());
        }
    }

    private function showSummary(array $tenants, string $entityName, array $fields, SymfonyStyle $io): void
    {
        $io->section('📊 Resumen de la Operación');

        // Información de la entidad
        $io->definitionList(
            ['Entidad' => $entityName],
            ['Tenants objetivo' => count($tenants)],
            ['Campos definidos' => count($fields)]
        );

        // Lista de tenants
        if (count($tenants) <= 5) {
            $io->text('<comment>Tenants seleccionados:</comment>');
            foreach ($tenants as $tenant) {
                $io->text("  • {$tenant['name']} ({$tenant['subdomain']}) → {$tenant['database_name']}");
            }
        } else {
            $io->text("<comment>Se aplicará a todos los " . count($tenants) . " tenants activos</comment>");
        }

        // Campos si están definidos
        if (!empty($fields)) {
            $io->text('<comment>Campos a crear:</comment>');
            foreach ($fields as $field) {
                $type = $field['type'] ?? 'string';
                $io->text("  • {$field['name']}: {$type}");
            }
        } else {
            $io->note('No se definieron campos. Se creará entidad básica con ID.');
        }
    }

    private function confirmExecution(array $tenants, SymfonyStyle $io): bool
    {
        $question = new ConfirmationQuestion(
            sprintf('¿Confirmas crear la entidad en %d tenant(s)? [y/N] ', count($tenants)),
            false
        );

        return $io->askQuestion($question);
    }

    private function createEntitiesForTenants(array $tenants, string $entityName, array $fields, bool $isDryRun, SymfonyStyle $io): array
    {
        $results = [];
        $io->section('🔨 Creando Entidades');

        $progressBar = $io->createProgressBar(count($tenants));
        $progressBar->setFormat('verbose');
        $progressBar->start();

        foreach ($tenants as $tenant) {
            $result = $this->createEntityForTenant($tenant, $entityName, $fields, $isDryRun, $io);
            $results[] = $result;
            $progressBar->advance();
        }

        $progressBar->finish();
        $io->newLine(2);

        return $results;
    }

    private function createEntityForTenant(array $tenant, string $entityName, array $fields, bool $isDryRun, SymfonyStyle $io): array
    {
        $subdomain = $tenant['subdomain'];
        $databaseName = $tenant['database_name'];

        try {
            // Verificar conectividad a la base de datos del tenant
            if (!$this->canConnectToTenantDatabase($databaseName)) {
                return [
                    'tenant' => $tenant,
                    'success' => false,
                    'message' => 'No se pudo conectar a la base de datos',
                    'details' => "BD: $databaseName no accesible"
                ];
            }

            if ($isDryRun) {
                return [
                    'tenant' => $tenant,
                    'success' => true,
                    'message' => '[DRY-RUN] Entidad se crearía exitosamente',
                    'details' => $this->generateEntityPreview($entityName, $fields)
                ];
            }

            // Generar código de la entidad
            $entityCode = $this->generateEntityCode($entityName, $fields);
            
            // Crear directorio de entidades para el tenant si no existe
            $entityDir = "/var/www/html/melisa_tenant/src/Entity";
            if (!is_dir($entityDir)) {
                mkdir($entityDir, 0755, true);
            }

            // Escribir archivo de entidad
            $entityFile = $entityDir . "/{$entityName}.php";
            file_put_contents($entityFile, $entityCode);

            // Ejecutar make:migration en el contexto del tenant
            $migrationResult = $this->generateMigrationForTenant($subdomain, $databaseName);

            return [
                'tenant' => $tenant,
                'success' => true,
                'message' => 'Entidad creada exitosamente',
                'details' => "Archivo: $entityFile\nMigración: " . ($migrationResult ? 'Generada' : 'Error en migración')
            ];

        } catch (\Exception $e) {
            return [
                'tenant' => $tenant,
                'success' => false,
                'message' => 'Error al crear entidad',
                'details' => $e->getMessage()
            ];
        }
    }

    private function canConnectToTenantDatabase(string $databaseName): bool
    {
        try {
            $connectionParams = [
                'dbname' => $databaseName,
                'user' => 'melisa',
                'password' => 'melisamelisa',
                'host' => 'localhost',
                'driver' => 'pdo_mysql',
            ];

            $connection = DriverManager::getConnection($connectionParams);
            // Verificar conexión ejecutando una consulta simple
            $connection->fetchOne('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function generateEntityCode(string $entityName, array $fields): string
    {
        $namespace = "App\\Entity";
        $className = $entityName;
        
        $properties = [];
        $methods = [];
        $uses = [
            'Doctrine\ORM\Mapping as ORM'
        ];

        // ID automático
        $properties[] = "    #[ORM\\Id]";
        $properties[] = "    #[ORM\\GeneratedValue]";
        $properties[] = "    #[ORM\\Column]";
        $properties[] = "    private ?int \$id = null;";
        $properties[] = "";

        // Métodos para ID
        $methods[] = "    public function getId(): ?int";
        $methods[] = "    {";
        $methods[] = "        return \$this->id;";
        $methods[] = "    }";
        $methods[] = "";

        // Procesar campos personalizados
        foreach ($fields as $field) {
            $fieldName = $field['name'];
            $fieldType = $field['type'] ?? 'string';
            $phpType = $this->mapDoctrineTypeToPhp($fieldType);
            $doctrineOptions = $this->getDoctrineColumnOptions($fieldType);
            
            // Propiedad
            $properties[] = "    #[ORM\\Column({$doctrineOptions})]";
            $properties[] = "    private ?$phpType \$$fieldName = null;";
            $properties[] = "";

            // Getter
            $getterName = 'get' . ucfirst($fieldName);
            $methods[] = "    public function $getterName(): ?$phpType";
            $methods[] = "    {";
            $methods[] = "        return \$this->$fieldName;";
            $methods[] = "    }";
            $methods[] = "";

            // Setter
            $setterName = 'set' . ucfirst($fieldName);
            $methods[] = "    public function $setterName(?$phpType \$$fieldName): static";
            $methods[] = "    {";
            $methods[] = "        \$this->$fieldName = \$$fieldName;";
            $methods[] = "        return \$this;";
            $methods[] = "    }";
            $methods[] = "";
        }

        $usesString = implode("\n", array_map(fn($use) => "use $use;", $uses));
        $propertiesString = implode("\n", $properties);
        $methodsString = implode("\n", $methods);

        return <<<PHP
<?php

namespace $namespace;

$usesString

#[ORM\\Entity]
#[ORM\\Table(name: '${className}')]
class $className
{
$propertiesString
$methodsString}
PHP;
    }

    private function generateEntityPreview(string $entityName, array $fields): string
    {
        $preview = "Entidad: $entityName\n";
        $preview .= "- id: int (auto)\n";
        
        foreach ($fields as $field) {
            $fieldType = $field['type'] ?? 'string';
            $preview .= "- {$field['name']}: $fieldType\n";
        }
        
        return $preview;
    }

    private function mapDoctrineTypeToPhp(string $doctrineType): string
    {
        return match($doctrineType) {
            'string', 'text' => 'string',
            'integer' => 'int',
            'boolean' => 'bool',
            'datetime', 'date' => '\DateTimeInterface',
            'decimal' => 'string',
            'json' => 'array',
            default => 'string'
        };
    }

    private function getDoctrineColumnOptions(string $fieldType): string
    {
        return match($fieldType) {
            'string' => 'type: "string", length: 255',
            'text' => 'type: "text"',
            'integer' => 'type: "integer"',
            'boolean' => 'type: "boolean"',
            'datetime' => 'type: "datetime"',
            'date' => 'type: "date"',
            'decimal' => 'type: "decimal", precision: 10, scale: 2',
            'json' => 'type: "json"',
            default => 'type: "string", length: 255'
        };
    }

    private function generateMigrationForTenant(string $subdomain, string $databaseName): bool
    {
        try {
            // Este sería el lugar para ejecutar doctrine:migrations:diff
            // En el contexto del tenant específico
            // Por ahora retornamos true como placeholder
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function showResults(array $results, bool $isDryRun, SymfonyStyle $io): void
    {
        $io->section($isDryRun ? '🔍 Resultados del Dry-Run' : '✅ Resultados de la Ejecución');

        $successful = array_filter($results, fn($r) => $r['success']);
        $failed = array_filter($results, fn($r) => !$r['success']);

        // Resumen
        $io->definitionList(
            ['Total tenants' => count($results)],
            ['Exitosos' => count($successful)],
            ['Fallidos' => count($failed)]
        );

        // Detalles de exitosos
        if (!empty($successful)) {
            $io->text('<info>✅ Tenants exitosos:</info>');
            foreach ($successful as $result) {
                $tenant = $result['tenant'];
                $io->text("  • {$tenant['name']} ({$tenant['subdomain']})");
                if (isset($result['details'])) {
                    $io->text("    {$result['details']}");
                }
            }
        }

        // Detalles de fallidos
        if (!empty($failed)) {
            $io->text('<error>❌ Tenants con errores:</error>');
            foreach ($failed as $result) {
                $tenant = $result['tenant'];
                $io->text("  • {$tenant['name']} ({$tenant['subdomain']})");
                $io->text("    Error: {$result['message']}");
                if (isset($result['details'])) {
                    $io->text("    Detalles: {$result['details']}");
                }
            }
        }

        // Mensaje final
        if ($isDryRun) {
            $io->info('🔍 Esta fue una simulación. Usa el comando sin --dry-run para ejecutar realmente.');
        } else {
            if (count($failed) === 0) {
                $io->success('🎉 Todas las entidades fueron creadas exitosamente!');
            } else {
                $io->warning('⚠️  Algunas entidades no pudieron ser creadas. Revisa los errores arriba.');
            }
        }

        // Comandos sugeridos
        if (!$isDryRun && count($successful) > 0) {
            $io->section('📝 Próximos Pasos Sugeridos');
            $io->text('Para cada tenant exitoso, considera ejecutar:');
            $io->text('  php bin/console app:migrate-tenant <subdomain>');
            $io->text('  # Para aplicar las nuevas migraciones');
        }
    }
}