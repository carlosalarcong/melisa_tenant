<?php

namespace App\Controller\Dashboard;

use App\Service\RouteResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controlador base para todos los dashboards
 * Proporciona funcionalidad común para resolución dinámica de rutas y plantillas
 */
abstract class AbstractDashboardController extends AbstractController
{
    protected RouteResolver $routeResolver;

    public function __construct(
        RouteResolver $routeResolver
    ) {
        $this->routeResolver = $routeResolver;
    }

    /**
     * Resuelve dinámicamente la plantilla del dashboard según el tenant
     */
    protected function resolveDashboardTemplate(string $tenantSubdomain): string
    {
        return $this->routeResolver->getDashboardTemplate($tenantSubdomain);
    }

    /**
     * Construye el menú básico común para todos los dashboards
     */
    protected function buildBaseMenu(string $tenantSubdomain): array
    {
        return [
            'dashboard' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_dashboard'),
            'pacientes' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_pacientes'),
            'citas' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_citas'),
            'mantenedores' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_mantenedores'),
            'reportes' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_reportes'),
            'configuracion' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_configuracion'),
        ];
    }

    /**
     * Resuelve el template y menú dinámicamente y renderiza la vista
     */
    protected function renderDashboard(string $tenantSubdomain, array $data): \Symfony\Component\HttpFoundation\Response
    {
        // Resolver plantilla y menú dinámicamente usando RouteResolver
        $template = $this->routeResolver->resolveTemplate($tenantSubdomain, 'dashboard');
        $menuRoutes = $this->buildDynamicMenu($tenantSubdomain);
        
        // Agregar menú a los datos
        $data['menu_routes'] = $menuRoutes;
        
        return $this->render($template, $data);
    }

    /**
     * Método abstracto que cada dashboard debe implementar para su menú específico
     * Por defecto puede retornar solo el menú base
     */
    protected function buildDynamicMenu(string $tenantSubdomain): array
    {
        return $this->buildBaseMenu($tenantSubdomain);
    }
}