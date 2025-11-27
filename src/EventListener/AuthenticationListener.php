<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener que verifica autenticación basada en sesión
 * Priority: 10 (después de listeners de tenant, antes de controladores)
 */
class AuthenticationListener implements EventSubscriberInterface
{
    public function __construct(
    ) {}
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 10]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {

    }

    /*
    private array $publicRoutes = [
        'app_login',
        'app_logout',
        'app_tenants_list',
    ];

    private array $publicPaths = [
        '/login',
        '/logout',
        '/api/tenants',
    ];

    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 10]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        $route = $request->attributes->get('_route');
        $path = $request->getPathInfo();

        // Permitir rutas públicas
        if ($this->isPublicRoute($route, $path)) {
            return;
        }

        // Permitir acceso a assets (CSS, JS, imágenes)
        if ($this->isAssetPath($path)) {
            return;
        }

        // Verificar si el usuario está logueado
        $isLoggedIn = $session->get('logged_in', false);

        if (!$isLoggedIn) {
            // Redirigir al login
            $loginUrl = $this->urlGenerator->generate('app_login');
            $event->setResponse(new RedirectResponse($loginUrl));
        }
    }

    private function isPublicRoute(?string $route, string $path): bool
    {
        // Verificar rutas públicas por nombre
        if ($route && in_array($route, $this->publicRoutes, true)) {
            return true;
        }

        // Verificar rutas públicas por path
        foreach ($this->publicPaths as $publicPath) {
            if (str_starts_with($path, $publicPath)) {
                return true;
            }
        }

        return false;
    }

    private function isAssetPath(string $path): bool
    {
        // Permitir profiler y debug toolbar en desarrollo
        if (str_starts_with($path, '/_')) {
            return true;
        }

        // Permitir assets estáticos
        $assetExtensions = ['.css', '.js', '.jpg', '.jpeg', '.png', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot'];
        
        foreach ($assetExtensions as $extension) {
            if (str_ends_with($path, $extension)) {
                return true;
            }
        }

        return false;
    }
    */
}
