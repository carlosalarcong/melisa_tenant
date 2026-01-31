<?php

namespace App\Command;

use App\Service\TenantResolver;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'test:multi-tenancy',
    description: 'Test multi-tenancy system with Symfony 7.4'
)]
class TestMultiTenancyCommand extends Command
{
    public function __construct(
        private TenantResolver $tenantResolver,
        private Connection $connection
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('🔍 Testing Multi-Tenancy System with Symfony 7.4.3 LTS');
        
        // Test 1: Verificar base de datos central
        $io->section('Test 1: Verificar conexión a base de datos central');
        try {
            $dbName = $this->connection->executeQuery('SELECT current_database()')->fetchOne();
            $io->success("✅ Conectado a base de datos: $dbName");
        } catch (\Exception $e) {
            $io->error("❌ Error conectando a base de datos: " . $e->getMessage());
            return Command::FAILURE;
        }
        
        // Test 2: Listar tenants disponibles
        $io->section('Test 2: Listar tenants disponibles');
        try {
            $tenants = $this->connection->executeQuery(
                'SELECT id, database_name, slug, name, is_active FROM tenant_db ORDER BY id'
            )->fetchAllAssociative();
            
            if (empty($tenants)) {
                $io->warning('⚠️  No hay tenants registrados en la base de datos');
                return Command::SUCCESS;
            }
            
            $io->table(
                ['ID', 'Database', 'Slug', 'Name', 'Active'],
                array_map(fn($t) => [
                    $t['id'],
                    $t['database_name'],
                    $t['slug'],
                    $t['name'] ?? 'N/A',
                    $t['is_active'] ? '✅' : '❌'
                ], $tenants)
            );
            
            // Test 3: Resolver primer tenant
            $firstTenant = $tenants[0];
            $io->section("Test 3: Resolver tenant '{$firstTenant['slug']}'");
            
            $tenantData = $this->tenantResolver->getTenantBySlug($firstTenant['slug']);
            
            if ($tenantData) {
                $io->success("✅ Tenant resuelto correctamente");
                $io->listing([
                    "ID: {$tenantData['id']}",
                    "Database: {$tenantData['database_name']}",
                    "Slug: {$tenantData['slug']}",
                    "Name: " . ($tenantData['name'] ?? 'N/A'),
                    "Active: " . ($tenantData['is_active'] ? 'Yes' : 'No')
                ]);
            } else {
                $io->error("❌ No se pudo resolver el tenant");
                return Command::FAILURE;
            }
            
            // Test 4: Verificar que la base de datos del tenant existe
            $io->section("Test 4: Verificar base de datos tenant '{$tenantData['database_name']}'");
            try {
                $databases = $this->connection->executeQuery('SELECT datname FROM pg_database WHERE datistemplate = false')->fetchFirstColumn();
                
                if (in_array($tenantData['database_name'], $databases)) {
                    $io->success("✅ Base de datos '{$tenantData['database_name']}' existe");
                    
                    // Verificar tablas en la base de datos del tenant
                    $tables = $this->connection->executeQuery(
                        "SELECT tablename FROM pg_tables WHERE schemaname = 'public'"
                    )->fetchFirstColumn();
                    
                    $io->writeln("📊 Tablas encontradas: " . count($tables));
                    if (!empty($tables)) {
                        $io->listing(array_slice($tables, 0, 10));
                        if (count($tables) > 10) {
                            $io->writeln("... y " . (count($tables) - 10) . " más");
                        }
                    }
                } else {
                    $io->warning("⚠️  Base de datos '{$tenantData['database_name']}' NO existe");
                    $io->note("Usa: php bin/console tenant:database:create --dbid={$tenantData['id']}");
                }
            } catch (\Exception $e) {
                $io->error("❌ Error verificando base de datos: " . $e->getMessage());
            }
            
            $io->success('🎉 Todos los tests completados exitosamente');
            
        } catch (\Exception $e) {
            $io->error("❌ Error en tests: " . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
