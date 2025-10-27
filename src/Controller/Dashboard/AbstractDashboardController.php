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
     * Verifica si existe controlador específico para el tenant
     */
    protected function hasSpecificController(string $tenantSubdomain, string $controller): bool
    {
        return $this->controllerResolver->controllerExistsForTenant($tenantSubdomain, $controller);
    }

    /**
     * Obtiene datos del tenant con fallbacks robustos usando DynamicControllerResolver
     * Centraliza la lógica para evitar repetición en cada controlador
     */
    protected function getTenantData(): array
    {
        return $this->controllerResolver->getGuaranteedTenant();
    }

    /**
     * Obtiene información de debug del tenant
     */
    protected function getTenantDebugInfo(string $tenantSubdomain): array
    {
        return $this->controllerResolver->getDebugInfo($tenantSubdomain);
    }
}