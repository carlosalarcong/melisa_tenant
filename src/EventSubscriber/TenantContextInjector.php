<?php

namespace App\EventSubscriber;

use App\Controller\AbstractTenantAwareController;
use App\Service\TenantContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Log\LoggerInterface;

/**
 * Inyecta automáticamente el contexto del tenant en controladores
 * que extienden AbstractTenantAwareController
 * 
 * Hace que el acceso al tenant sea completamente transparente:
 * - No requiere inyección en constructor
 * - No requiere pasar TenantContext manualmente
 * - Solo extender AbstractTenantAwareController y usar $this->tenant
 */
class TenantContextInjector implements EventSubscriberInterface
{
    public function __construct(
        private TenantContext $tenantContext,
        private LoggerInterface $logger
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            // Ejecutar justo después de que se determine el controlador
            // pero antes de ejecutarlo
            KernelEvents::CONTROLLER => ['onKernelController', 10],
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // Si es un array [objeto, 'método'], tomar el objeto
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        // Solo procesar si es un AbstractTenantAwareController
        if (!$controller instanceof AbstractTenantAwareController) {
            return;
        }

        // Obtener tenant actual desde TenantContext
        $tenant = $this->tenantContext->getCurrentTenant();

        // Usar reflection para inyectar las propiedades
        $reflection = new \ReflectionClass($controller);

        // Inyectar tenant completo
        $tenantProperty = $reflection->getProperty('tenant');
        $tenantProperty->setAccessible(true);
        $tenantProperty->setValue($controller, $tenant);

        // Inyectar subdomain
        $subdomainProperty = $reflection->getProperty('tenantSubdomain');
        $subdomainProperty->setAccessible(true);
        $subdomainProperty->setValue($controller, $tenant['subdomain'] ?? 'default');

        // Inyectar nombre
        $nameProperty = $reflection->getProperty('tenantName');
        $nameProperty->setAccessible(true);
        $nameProperty->setValue($controller, $tenant['name'] ?? 'Default Tenant');

        $this->logger->debug('✅ Tenant context inyectado automáticamente en controlador', [
            'controller' => get_class($controller),
            'tenant_subdomain' => $tenant['subdomain'] ?? 'default',
            'tenant_name' => $tenant['name'] ?? 'Default'
        ]);
    }
}
