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
        private LoggerInterface $logger
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
        // No resolver controladores que ya están específicos del tenant
        if (str_contains($controller, ucfirst($tenantSubdomain))) {
            return false;
        }

        // No resolver controladores de sistema (Security, Login, etc.)
        $systemControllers = [
            'App\\Controller\\LoginController',
            'App\\Controller\\SecurityController',
            'App\\Controller\\LocaleController',
            'Symfony\\',
        ];

        foreach ($systemControllers as $systemController) {
            if (str_starts_with($controller, $systemController)) {
                return false;
            }
        }

        // No resolver mantenedores básicos - son centrales para todos los tenants
        $centralControllers = [
            'App\\Controller\\Mantenedores\\Basico\\',
            'App\\Controller\\Mantenedores\\',  // Todos los mantenedores son centrales ahora
        ];

        foreach ($centralControllers as $centralController) {
            if (str_starts_with($controller, $centralController)) {
                return false;
            }
        }

        // Por defecto, resolver TODOS los controladores de App\Controller\
        // excepto los que específicamente excluimos arriba
        // Esto hace el sistema escalable - cualquier nuevo controlador 
        // automáticamente será resuelto dinámicamente por tenant
        if (str_starts_with($controller, 'App\\Controller\\')) {
            return true;
        }

        return false;
    }
}