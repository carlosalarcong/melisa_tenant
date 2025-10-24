<?php

namespace App\Service;

use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class RouteResolver
{
    public function __construct(
        private RouterInterface $router,
        private Environment $twig
    ) {}

    /**
     * Resuelve dinámicamente cualquier ruta basada en tenant y funcionalidad
     * 
     * @param string $tenantSubdomain El subdomain del tenant (ej: 'melisalacolina')
     * @param string $routeBase El prefijo base de la ruta (ej: 'app_dashboard', 'app_mantenedores', 'app_reportes')
     * @param string|null $fallbackRoute Ruta de fallback si no encuentra específica (opcional)
     * @return string La ruta encontrada
     */
    public function resolveRoute(string $tenantSubdomain, string $routeBase, ?string $fallbackRoute = null): string
    {
        $routes = $this->router->getRouteCollection();
        
        // Prioridad 1: Buscar ruta específica del tenant
        // Ej: app_dashboard_melisalacolina, app_mantenedores_melisahospital
        $tenantSpecificRoute = "{$routeBase}_{$tenantSubdomain}";
        if ($routes->get($tenantSpecificRoute)) {
            return $tenantSpecificRoute;
        }
        
        // Prioridad 2: Buscar ruta base universal (sin sufijo)
        // Ej: app_dashboard, app_mantenedores
        if ($routes->get($routeBase)) {
            return $routeBase;
        }
        
        // Prioridad 3: Buscar ruta default específica
        // Ej: app_dashboard_default, app_mantenedores_default
        $defaultRoute = "{$routeBase}_default";
        if ($routes->get($defaultRoute)) {
            return $defaultRoute;
        }
        
        // Prioridad 4: Usar fallback proporcionado
        if ($fallbackRoute && $routes->get($fallbackRoute)) {
            return $fallbackRoute;
        }
        
        // Prioridad 5: Buscar cualquier ruta que empiece con el prefijo
        foreach ($routes->all() as $name => $route) {
            if (str_starts_with($name, $routeBase)) {
                return $name;
            }
        }
        
        // Último recurso: redirigir al login
        return 'app_login';
    }

    /**
     * Resuelve dinámicamente cualquier plantilla basada en tenant y tipo
     * 
     * @param string $tenantSubdomain El subdomain del tenant (ej: 'melisalacolina')
     * @param string $templateBase El directorio base de la plantilla (ej: 'dashboard', 'mantenedores', 'reportes')
     * @param string $templateFile El archivo de plantilla (ej: 'index.html.twig', 'list.html.twig')
     * @param string|null $fallbackTemplate Plantilla de fallback si no encuentra específica (opcional)
     * @return string La plantilla encontrada
     */
    public function resolveTemplate(string $tenantSubdomain, string $templateBase, string $templateFile = 'index.html.twig', ?string $fallbackTemplate = null): string
    {
        // Prioridad 1: Buscar plantilla específica del tenant
        // Ej: dashboard/melisalacolina/index.html.twig, mantenedores/melisahospital/list.html.twig
        $tenantSpecificTemplate = "{$templateBase}/{$tenantSubdomain}/{$templateFile}";
        if ($this->twig->getLoader()->exists($tenantSpecificTemplate)) {
            return $tenantSpecificTemplate;
        }
        
        // Prioridad 2: Buscar plantilla base universal (sin tenant)
        // Ej: dashboard/index.html.twig, mantenedores/list.html.twig
        $baseTemplate = "{$templateBase}/{$templateFile}";
        if ($this->twig->getLoader()->exists($baseTemplate)) {
            return $baseTemplate;
        }
        
        // Prioridad 3: Buscar plantilla default específica
        // Ej: dashboard/default/index.html.twig, mantenedores/default/list.html.twig
        $defaultTemplate = "{$templateBase}/default/{$templateFile}";
        if ($this->twig->getLoader()->exists($defaultTemplate)) {
            return $defaultTemplate;
        }
        
        // Prioridad 4: Usar fallback proporcionado
        if ($fallbackTemplate && $this->twig->getLoader()->exists($fallbackTemplate)) {
            return $fallbackTemplate;
        }
        
        // Último recurso: plantilla base genérica
        return 'base.html.twig';
    }

    /**
     * Métodos de conveniencia para plantillas comunes
     */
    public function getDashboardTemplate(string $tenantSubdomain): string
    {
        return $this->resolveTemplate($tenantSubdomain, 'dashboard');
    }

    public function getMantenedoresTemplate(string $tenantSubdomain, string $view = 'index.html.twig'): string
    {
        return $this->resolveTemplate($tenantSubdomain, 'mantenedores', $view);
    }

    public function getReportesTemplate(string $tenantSubdomain, string $view = 'index.html.twig'): string
    {
        return $this->resolveTemplate($tenantSubdomain, 'reportes', $view);
    }

    public function getFormTemplate(string $tenantSubdomain, string $entity): string
    {
        return $this->resolveTemplate($tenantSubdomain, 'forms', "{$entity}.html.twig", 'forms/default.html.twig');
    }

    /**
     * Verifica si existe una ruta específica para el tenant
     */
    public function hasSpecificRoute(string $tenantSubdomain, string $routeBase): bool
    {
        $routes = $this->router->getRouteCollection();
        $tenantSpecificRoute = "{$routeBase}_{$tenantSubdomain}";
        
        return $routes->get($tenantSpecificRoute) !== null;
    }

    /**
     * Obtiene todas las rutas disponibles para un tenant específico
     */
    public function getTenantRoutes(string $tenantSubdomain): array
    {
        $routes = $this->router->getRouteCollection();
        $tenantRoutes = [];
        
        foreach ($routes->all() as $name => $route) {
            if (str_ends_with($name, "_{$tenantSubdomain}")) {
                $tenantRoutes[] = $name;
            }
        }
        
        return $tenantRoutes;
    }
}