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
    private ?string $currentTenant = null;

    public function __construct(
        private TenantResolver $tenantResolver,
        private Connection $connection,
        private LoggerInterface $logger
    ) {}

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

        // Extraer tenant desde el subdominio
        $tenant = $this->extractTenantFromHost($host);
        
        if ($tenant && $tenant !== $this->currentTenant) {
            $this->logger->info('Tenant detectado', ['tenant' => $tenant]);
            $this->configureTenantDatabase($tenant);
        }
    }

    private function extractTenantFromHost(string $host): ?string
    {
        // Extraer tenant desde el subdominio (ej: melisahospital.melisaupgrade.prod -> melisahospital)
        $parts = explode('.', $host);

        if (count($parts) >= 2) {
            $subdomain = $parts[0];

            // Omitir subdominios comunes
            if (!in_array($subdomain, ['www', 'api', 'admin'])) {
                return $subdomain;
            }
        }

        return $this->getFallbackTenant();
    }

    private function getFallbackTenant(): ?string
    {
        // Para desarrollo o escenarios de fallback
        return 'melisahospital';
    }

    private function configureTenantDatabase(string $tenant): void
    {
        try {
            $this->logger->info('Configurando database para tenant', ['tenant' => $tenant]);

            // Cerrar conexión existente si está conectada
            if ($this->connection->isConnected()) {
                $this->connection->close();
            }

            // Obtener parámetros actuales de la conexión
            $params = $this->connection->getParams();
            $originalDb = $params['dbname'] ?? 'unknown';

            $this->logger->info('Cambiando database', ['from' => $originalDb, 'to' => $tenant]);

            // Actualizar el nombre de la base de datos directamente en la conexión
            $params['dbname'] = $tenant;

            // Usar reflexión para actualizar los parámetros de la conexión
            $reflection = new \ReflectionObject($this->connection);
            $paramsProperty = $reflection->getProperty('params');
            $paramsProperty->setAccessible(true);
            $paramsProperty->setValue($this->connection, $params);

            // Doctrine reconectará automáticamente con la nueva base de datos en la siguiente consulta (lazy connection)
            // No es necesario llamar connect() explícitamente - está deprecated en DBAL 4.x

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