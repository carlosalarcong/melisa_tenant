<?php

namespace App\EventListener;

use App\Service\TenantResolver;
use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Log\LoggerInterface;

/**
 * Event Listener para configurar conexión de base de datos por tenant
 */
class TenantConnectionListener implements EventSubscriberInterface
{
    private TenantResolver $tenantResolver;
    private Connection $connection;
    private LoggerInterface $logger;
    private ?string $currentTenant = null;

    public function __construct(
        TenantResolver $tenantResolver,
        Connection $connection,
        LoggerInterface $logger
    ) {
        $this->tenantResolver = $tenantResolver;
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 1000],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $host = $request->getHost();
        
        $this->logger->info('TenantConnectionListener ejecutándose', ['host' => $host]);

        // Extract tenant from subdomain
        $tenant = $this->extractTenantFromHost($host);
        
        if ($tenant && $tenant !== $this->currentTenant) {
            $this->logger->info('Tenant detectado', ['tenant' => $tenant]);
            $this->configureTenantDatabase($tenant);
        }
    }

    private function extractTenantFromHost(string $host): ?string
    {
        // Extract tenant from subdomain (e.g., melisahospital.melisaupgrade.prod -> melisahospital)
        $parts = explode('.', $host);
        
        if (count($parts) >= 2) {
            $subdomain = $parts[0];
            
            // Skip common subdomains
            if (!in_array($subdomain, ['www', 'api', 'admin'])) {
                return $subdomain;
            }
        }
        
        return $this->getFallbackTenant();
    }

    private function getFallbackTenant(): ?string
    {
        // For development or fallback scenarios
        return 'melisahospital';
    }

    private function configureTenantDatabase(string $tenant): void
    {
        try {
            $this->logger->info('Configurando database para tenant', ['tenant' => $tenant]);
            
            // Close existing connection if connected
            if ($this->connection->isConnected()) {
                $this->connection->close();
            }
            
            // Get current connection parameters
            $params = $this->connection->getParams();
            $originalDb = $params['dbname'] ?? 'unknown';
            
            $this->logger->info('Cambiando database', ['from' => $originalDb, 'to' => $tenant]);
            
            // Update database name directly in the connection
            $params['dbname'] = $tenant;
            
            // Use reflection to update the connection parameters
            $reflection = new \ReflectionObject($this->connection);
            $paramsProperty = $reflection->getProperty('params');
            $paramsProperty->setAccessible(true);
            $paramsProperty->setValue($this->connection, $params);
            
            // Force reconnection with new database
            $this->connection->connect();
            
            $this->currentTenant = $tenant;
            
            $this->logger->info('Database configurada exitosamente', ['tenant' => $tenant]);
            
        } catch (\Exception $e) {
            $this->logger->error('Error configurando tenant database', [
                'tenant' => $tenant,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}