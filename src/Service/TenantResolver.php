<?php

namespace App\Service;

use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpFoundation\Request;

class TenantResolver
{
    private $centralDbConfig = [
        'host' => 'localhost',
        'port' => 3306,
        'dbname' => 'melisa_central',
        'user' => 'melisa',
        'password' => 'melisamelisa',
        'driver' => 'pdo_mysql',
    ];

    /**
     * Resuelve el tenant desde el subdomain de la URL
     */
    public function resolveTenantFromRequest(Request $request): ?array
    {
        $host = $request->getHost();
        
        // Extraer subdomain de la URL
        // Ejemplo: melisalacolina.melisaupgrade.prod → melisalacolina
        $parts = explode('.', $host);
        
        if (count($parts) < 2) {
            return null; // No hay subdomain
        }
        
        $slug = $parts[0];
        
        return $this->getTenantBySlug($slug);
    }

    /**
     * Obtiene los datos del tenant desde la BD central
     */
    public function getTenantBySlug(string $slug): ?array
    {
        try {
            $connection = DriverManager::getConnection($this->centralDbConfig);
            
            $query = '
                SELECT id, name, subdomain, database_name, rut_empresa,
                       COALESCE(domain, "localhost") as host,
                       host_port,
                       COALESCE(db_user, "melisa") as db_user,
                       COALESCE(db_password, "melisamelisa") as db_password,
                       COALESCE(driver, "mysql") as driver,
                       is_active, language
                FROM tenant 
                WHERE subdomain = ? AND is_active = 1
            ';
            
            // Log para debug
            error_log("TenantResolver: Ejecutando query: " . $query . " con slug: " . $slug);
            
            $result = $connection->executeQuery($query, [$slug]);
            return $result->fetchAssociative() ?: null;
            
        } catch (\Exception $e) {
            throw new \Exception('Error resolviendo tenant: ' . $e->getMessage());
        }
    }

    /**
     * Crea conexión a la base de datos del tenant
     */
    public function createTenantConnection(array $tenant): \Doctrine\DBAL\Connection
    {
        $tenantDbConfig = [
            'host' => $tenant['host'] ?? 'localhost',
            'port' => $tenant['host_port'] ?? 3306,
            'dbname' => $tenant['database_name'],
            'user' => $tenant['db_user'] ?? 'melisa',
            'password' => $tenant['db_password'] ?? 'melisamelisa',
            'driver' => 'pdo_mysql',
        ];

        return DriverManager::getConnection($tenantDbConfig);
    }

    /**
     * Lista todos los tenants activos (para el selector)
     */
    public function getAllActiveTenants(): array
    {
        try {
            $connection = DriverManager::getConnection($this->centralDbConfig);
            
            $query = 'SELECT subdomain, name FROM tenant WHERE is_active = 1 ORDER BY name';
            $result = $connection->executeQuery($query);
            
            return $result->fetchAllAssociative();
            
        } catch (\Exception $e) {
            throw new \Exception('Error obteniendo tenants: ' . $e->getMessage());
        }
    }
}