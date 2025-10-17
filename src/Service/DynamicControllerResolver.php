<?php

namespace App\Service;

use App\Entity\Tenant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;

/**
 * Resolvedor dinámico de controladores multi-tenant
 * Maneja la resolución de controladores específicos por tenant con fallbacks
 */
class DynamicControllerResolver
{
    public function __construct(
        private TenantContext $tenantContext,
        private LoggerInterface $logger
    ) {}

    /**
     * Resuelve un controlador según el patrón y el tenant actual
     */
    public function resolve(Request $request): callable
    {
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);
        
        // Obtener configuración de la ruta
        $controllerPattern = $routeParams['_controller_pattern'] ?? null;
        $fallbackController = $routeParams['_fallback_controller'] ?? null;
        $tenantSpecific = $routeParams['_tenant_specific'] ?? [];
        
        if (!$controllerPattern) {
            throw new NotFoundHttpException('Patrón de controlador no definido para la ruta: ' . $route);
        }
        
        $tenant = $this->tenantContext->getCurrentTenant();
        $tenantKey = $tenant ? ucfirst($tenant['subdomain']) : 'Default';
        
        $this->logger->debug('Resolviendo controlador', [
            'route' => $route,
            'tenant' => $tenantKey,
            'pattern' => $controllerPattern,
            'fallback' => $fallbackController
        ]);
        
        // Verificar si la ruta es específica para ciertos tenants
        if (!empty($tenantSpecific) && !in_array($tenantKey, $tenantSpecific)) {
            if ($fallbackController) {
                return $this->createCallable($fallbackController);
            }
            throw new NotFoundHttpException('Ruta no disponible para el tenant actual: ' . $tenantKey);
        }
        
        // Intentar resolver el controlador específico del tenant
        $specificController = str_replace('{tenant}', $tenantKey, $controllerPattern);
        
        if ($this->controllerExists($specificController)) {
            $this->logger->debug('Controlador específico encontrado', ['controller' => $specificController]);
            return $this->createCallable($specificController);
        }
        
        // Fallback al controlador por defecto
        if ($fallbackController && $this->controllerExists($fallbackController)) {
            $this->logger->debug('Usando controlador fallback', ['controller' => $fallbackController]);
            return $this->createCallable($fallbackController);
        }
        
        throw new NotFoundHttpException('Controlador no encontrado para la ruta: ' . $route);
    }

    /**
     * Método legacy para compatibilidad con código existente
     */
    public function resolveController(string $subdomain, string $controller, string $action = 'index'): string
    {
        // Para el dashboard, usar la estructura especial Dashboard/
        if ($controller === 'dashboard') {
            return $this->resolveDashboardController($subdomain, $action);
        }
        
        // Para mantenedores, usar la nueva estructura
        if ($controller === 'mantenedores') {
            return $this->resolveMantenedorController($subdomain, $action);
        }
        
        // Para otros controladores, usar la lógica original
        $tenantNamespace = ucfirst($subdomain);
        $controllerClass = ucfirst($controller) . 'Controller';
        
        // Ruta del controlador personalizado: App\Controller\Clinica1\PacientesController
        $customControllerPath = sprintf(
            'App\\Controller\\%s\\%s',
            $tenantNamespace,
            $controllerClass
        );
        
        // Si existe controlador personalizado en subcarpeta
        if (class_exists($customControllerPath)) {
            return $customControllerPath . '::' . $action;
        }
        
        // Fallback a DefaultController genérico
        return 'App\\Controller\\DefaultController::' . $action;
    }
    
    /**
     * Resuelve controladores de dashboard
     */
    private function resolveDashboardController(string $subdomain, string $action): string
    {
        // Intentar cargar controlador específico del tenant en Dashboard/
        $specificNamespace = 'App\\Controller\\Dashboard\\' . ucfirst($subdomain);
        $specificController = $specificNamespace . '\\DefaultController';
        
        if (class_exists($specificController) && method_exists($specificController, $action)) {
            return $specificController . '::' . $action;
        }
        
        // Fallback al controlador Dashboard por defecto
        $defaultController = 'App\\Controller\\Dashboard\\Default\\DefaultController';
        if (class_exists($defaultController) && method_exists($defaultController, $action)) {
            return $defaultController . '::' . $action;
        }
        
        // Último fallback
        return 'App\\Controller\\DefaultController::' . $action;
    }

    /**
     * Resuelve controladores de mantenedores con estructura jerárquica
     * NOTA: Los mantenedores básicos (Pais, Religion, Sexo, Region) ahora son centrales/globales
     * y no requieren resolución dinámica por tenant
     */
    private function resolveMantenedorController(string $subdomain, string $action): string
    {
        // Los mantenedores básicos ahora son directos y centrales:
        // - App\Controller\Mantenedores\Basico\PaisController
        // - App\Controller\Mantenedores\Basico\ReligionController  
        // - App\Controller\Mantenedores\Basico\SexoController
        // - App\Controller\Mantenedores\Basico\RegionController
        
        // Para mantenedores avanzados específicos por tenant (futuro)
        $tenantKey = ucfirst($subdomain);
        
        // Solo usar resolución dinámica para mantenedores específicos que lo requieran
        // Por ejemplo, funcionalidades IoT específicas de Melisawiclinic
        $specificController = "App\\Controller\\Mantenedores\\Avanzado\\{$tenantKey}\\{$action}Controller";
        if (class_exists($specificController)) {
            return $specificController . '::index';
        }
        
        // Fallback: redirigir a mantenedores básicos o dashboard
        return 'App\\Controller\\Dashboard\\DefaultController::mantenedores';
    }

    /**
     * Crea un callable desde un string de controlador
     */
    private function createCallable(string $controllerString): callable
    {
        if (strpos($controllerString, '::') === false) {
            throw new \InvalidArgumentException('Formato de controlador inválido: ' . $controllerString);
        }
        
        [$class, $method] = explode('::', $controllerString, 2);
        
        if (!class_exists($class)) {
            throw new NotFoundHttpException('Clase de controlador no encontrada: ' . $class);
        }
        
        if (!method_exists($class, $method)) {
            throw new NotFoundHttpException('Método no encontrado: ' . $controllerString);
        }
        
        return [$class, $method];
    }

    /**
     * Verifica si un controlador existe
     */
    private function controllerExists(string $controllerString): bool
    {
        if (strpos($controllerString, '::') === false) {
            return false;
        }
        
        [$class, $method] = explode('::', $controllerString, 2);
        
        return class_exists($class) && method_exists($class, $method);
    }
    
    /**
     * Obtiene controladores disponibles para un subdomain
     */
    public function getAvailableControllers(string $subdomain): array
    {
        $controllersPath = __DIR__ . '/../Controller/' . ucfirst($subdomain);
        
        if (!is_dir($controllersPath)) {
            return ['default']; // Solo DefaultController disponible
        }
        
        $controllers = [];
        $files = glob($controllersPath . '/*Controller.php');
        
        foreach ($files as $file) {
            $controllerName = basename($file, 'Controller.php');
            $controllers[] = strtolower($controllerName);
        }
        
        return $controllers;
    }

    /**
     * Obtiene mantenedores disponibles para un tenant
     * Actualizado para la nueva estructura de mantenedores centrales
     */
    public function getAvailableMantenedores(string $subdomain): array
    {
        $mantenedores = [];
        
        // Mantenedores básicos centrales (disponibles para todos los tenants)
        $basicosPath = __DIR__ . '/../Controller/Mantenedores/Basico';
        
        if (is_dir($basicosPath)) {
            $basicControllers = glob($basicosPath . '/*Controller.php');
            
            foreach ($basicControllers as $controllerFile) {
                $controllerName = basename($controllerFile, 'Controller.php');
                
                $mantenedores[] = [
                    'category' => 'basico',
                    'name' => strtolower($controllerName),
                    'label' => ucfirst($controllerName),
                    'is_central' => true,
                    'controller_path' => "App\\Controller\\Mantenedores\\Basico\\{$controllerName}Controller",
                    'route_prefix' => '/mantenedores/basico/' . strtolower($controllerName)
                ];
            }
        }
        
        // Mantenedores específicos por tenant (si existen)
        $tenantKey = ucfirst($subdomain);
        $tenantPath = __DIR__ . '/../Controller/Mantenedores/' . $tenantKey;
        
        if (is_dir($tenantPath)) {
            $tenantControllers = glob($tenantPath . '/*Controller.php');
            
            foreach ($tenantControllers as $controllerFile) {
                $controllerName = basename($controllerFile, 'Controller.php');
                
                $mantenedores[] = [
                    'category' => 'tenant_specific',
                    'name' => strtolower($controllerName),
                    'label' => ucfirst($controllerName),
                    'is_central' => false,
                    'tenant' => $subdomain,
                    'controller_path' => "App\\Controller\\Mantenedores\\{$tenantKey}\\{$controllerName}Controller",
                    'route_prefix' => '/mantenedores/' . strtolower($subdomain) . '/' . strtolower($controllerName)
                ];
            }
        }
        
        return $mantenedores;
    }
    
    /**
     * Verifica si un controlador específico existe para un tenant
     */
    public function controllerExistsForTenant(string $subdomain, string $controller): bool
    {
        $tenantNamespace = ucfirst($subdomain);
        $controllerClass = ucfirst($controller) . 'Controller';
        
        $customControllerPath = sprintf(
            'App\\Controller\\%s\\%s',
            $tenantNamespace,
            $controllerClass
        );
        
        return class_exists($customControllerPath);
    }
    
    /**
     * Obtiene la ruta de un controlador
     */
    public function getControllerPath(string $subdomain, string $controller): string
    {
        $tenantNamespace = ucfirst($subdomain);
        $controllerClass = ucfirst($controller) . 'Controller';
        
        return sprintf('App\\Controller\\%s\\%s', $tenantNamespace, $controllerClass);
    }

    /**
     * Obtiene información de depuración sobre la resolución de controladores
     */
    public function getDebugInfo(string $subdomain): array
    {
        return [
            'tenant' => $subdomain,
            'tenant_key' => ucfirst($subdomain),
            'available_controllers' => $this->getAvailableControllers($subdomain),
            'available_mantenedores' => $this->getAvailableMantenedores($subdomain),
            'dashboard_controller_exists' => $this->controllerExistsForTenant($subdomain, 'dashboard'),
            'mantenedores_path' => __DIR__ . '/../Controller/Mantenedores/Basico',
        ];
    }
}