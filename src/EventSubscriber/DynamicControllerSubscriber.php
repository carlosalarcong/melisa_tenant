<?php

namespace App\EventSubscriber;

use App\Service\DynamicControllerResolver;
use App\Service\TenantContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Log\LoggerInterface;

/**
 * Subscriber que resuelve controladores dinámicamente basado en el tenant
 * Inspirado en el patrón ControllerSubscriber pero usando DynamicControllerResolver
 */
class DynamicControllerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private DynamicControllerResolver $controllerResolver,
        private TenantContext $tenantContext,
        private LoggerInterface $logger,
        private array $excludedControllers = [],
        private array $excludedNamespaces = []
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            // Ejecutar después del LocaleListener pero antes del controlador
            KernelEvents::REQUEST => [['onKernelRequest', 15]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Solo procesar el request principal
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $originalController = $request->attributes->get('_controller');

        // Solo procesar si hay un controlador definido
        if (!$originalController || !is_string($originalController)) {
            return;
        }

        // Obtener el tenant actual
        $tenant = $this->tenantContext->getCurrentTenant();
        $tenantSubdomain = $tenant['subdomain'] ?? 'default';

        // Verificar si el controlador necesita resolución dinámica
        if (!$this->shouldResolveDynamically($originalController, $tenantSubdomain)) {
            return;
        }

        // Resolver el controlador dinámicamente
        $resolvedController = $this->controllerResolver->resolveControllerFromRoute(
            $originalController, 
            $tenantSubdomain
        );

        // Si el controlador cambió, actualizarlo en el request
        if ($resolvedController !== $originalController) {
            $request->attributes->set('_controller', $resolvedController);
            
            $this->logger->debug('Controlador resuelto dinámicamente por subscriber', [
                'tenant' => $tenantSubdomain,
                'original' => $originalController,
                'resolved' => $resolvedController,
                'route' => $request->attributes->get('_route'),
                'path' => $request->getPathInfo()
            ]);
        }
    }

    /**
     * Determina si un controlador debe ser resuelto dinámicamente
     */
    private function shouldResolveDynamically(string $controller, string $tenantSubdomain): bool
    {
        if (str_contains($controller, ucfirst($tenantSubdomain))) {
            return false;
        }

        foreach ($this->excludedControllers as $excludedController) {
            if ($controller === $excludedController) {
                $this->logger->debug('Controlador excluido por configuración exacta', [
                    'controller' => $controller,
                    'excluded' => $excludedController
                ]);
                return false;
            }
        }

        foreach ($this->excludedNamespaces as $excludedNamespace) {
            if (str_starts_with($controller, $excludedNamespace)) {
                $this->logger->debug('Controlador excluido por namespace', [
                    'controller' => $controller,
                    'namespace' => $excludedNamespace
                ]);
                return false;
            }
        }

        if (str_starts_with($controller, 'App\\Controller\\')) {
            $this->logger->debug('Controlador será resuelto dinámicamente', [
                'controller' => $controller,
                'tenant' => $tenantSubdomain
            ]);
            return true;
        }

        return false;
    }
}