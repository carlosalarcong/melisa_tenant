<?php

namespace App\Controller\Dashboard;

use App\Service\DynamicControllerResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

/**
 * Controlador base para todos los dashboards
 * Proporciona funcionalidad común para resolución dinámica de controladores y plantillas
 */
abstract class AbstractDashboardController extends AbstractController
{
    protected DynamicControllerResolver $controllerResolver;
    protected Environment $twig;

    public function __construct(DynamicControllerResolver $controllerResolver, Environment $twig)
    {
        // DynamicControllerResolver: Resuelve controladores específicos por tenant con fallbacks automáticos
        // Environment $twig: Verifica existencia de plantillas antes de renderizar para evitar errores
        $this->controllerResolver = $controllerResolver;
        $this->twig = $twig;
    }

    /**
     * Resuelve dinámicamente la plantilla del dashboard según el tenant
     */
    protected function resolveDashboardTemplate(string $tenantSubdomain): string
    {
        $tenantSpecificTemplate = "dashboard/{$tenantSubdomain}/default.html.twig";
        $defaultTemplate = "dashboard/default.html.twig";
        
        if ($this->twig->getLoader()->exists($tenantSpecificTemplate)) {
            return $tenantSpecificTemplate;
        }
        
        return $defaultTemplate;
    }

    /**
     * Construye menú dinámico básico
     */
    protected function buildDynamicMenu(string $tenantSubdomain): array
    {
        return [
            'dashboard' => ['url' => '/dashboard', 'label' => 'Dashboard'],
            'pacientes' => ['url' => '/pacientes', 'label' => 'Pacientes'],
            'citas' => ['url' => '/citas', 'label' => 'Citas'],
            'mantenedores' => ['url' => '/mantenedores', 'label' => 'Mantenedores'],
            'reportes' => ['url' => '/reportes', 'label' => 'Reportes'],
            'configuracion' => ['url' => '/configuracion', 'label' => 'Configuración'],
        ];
    }

    /**
     * Renderiza dashboard con plantilla y menú dinámicos
     */
    protected function renderDashboard(string $tenantSubdomain, array $data): \Symfony\Component\HttpFoundation\Response
    {
        $template = $this->resolveDashboardTemplate($tenantSubdomain);
        $menuRoutes = $this->buildDynamicMenu($tenantSubdomain);
        
        $data['menu_routes'] = $menuRoutes;
        
        return $this->render($template, $data);
    }

    /**
     * Verifica si existe controlador específico para el tenant
     */
    protected function hasSpecificController(string $tenantSubdomain, string $controller): bool
    {
        return $this->controllerResolver->controllerExistsForTenant($tenantSubdomain, $controller);
    }

    /**
     * Obtiene información de debug del tenant
     */
    protected function getTenantDebugInfo(string $tenantSubdomain): array
    {
        return $this->controllerResolver->getDebugInfo($tenantSubdomain);
    }
}