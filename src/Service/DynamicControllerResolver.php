<?php

namespace App\Service;

use App\Entity\Tenant;
use App\Service\TenantContext;
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
     * Resuelve dinámicamente cualquier controlador basado en tenant y estructura
     * Inspirado en el patrón de ControllerSubscriber pero con más control
     */
    public function resolveController(string $subdomain, string $controller, string $action = 'index'): string
    {
        $tenantKey = ucfirst($subdomain);
        $controllerClass = ucfirst($controller) . 'Controller';
        
        // Definir patrones de búsqueda en orden de prioridad
        $searchPatterns = [
            // 1. Controlador específico del tenant en estructura jerárquica
            "App\\Controller\\{$controller}\\{$tenantKey}\\{$controllerClass}",
            
            // 2. Controlador específico del tenant en carpeta del tenant
            "App\\Controller\\{$tenantKey}\\{$controllerClass}",
            
            // 3. Controlador específico en subcarpeta temática (Dashboard/, Mantenedores/, etc.)
            "App\\Controller\\{$controllerClass}\\{$tenantKey}\\DefaultController",
            
            // 4. Controlador default en estructura jerárquica
            "App\\Controller\\{$controller}\\Default\\{$controllerClass}",
            
            // 5. Controlador base universal
            "App\\Controller\\{$controllerClass}",
            
            // 6. Controlador default genérico
            "App\\Controller\\DefaultController"
        ];
        
        // Buscar el primer controlador que exista
        foreach ($searchPatterns as $controllerPath) {
            if (class_exists($controllerPath) && method_exists($controllerPath, $action)) {
                $this->logger->debug('Controlador dinámico resuelto', [
                    'tenant' => $subdomain,
                    'controller' => $controller,
                    'action' => $action,
                    'resolved_path' => $controllerPath,
                    'pattern_used' => $controllerPath
                ]);
                
                return $controllerPath . '::' . $action;
            }
        }
        
        // Si no encuentra nada, log de error y usar fallback absoluto
        $this->logger->error('No se pudo resolver controlador dinámico', [
            'tenant' => $subdomain,
            'controller' => $controller,
            'action' => $action,
            'patterns_tried' => $searchPatterns
        ]);
        
        return 'App\\Controller\\DefaultController::index';
    }

    /**
     * Resuelve controlador dinámicamente como lo haría un ControllerSubscriber
     * Permite resolución automática basada en el tenant sin configuración manual
     */
    public function resolveControllerFromRoute(string $originalController, string $tenantSubdomain): string
    {
        // Analizar el controlador original
        if (strpos($originalController, '::') === false) {
            return $originalController; // No es un controlador válido
        }
        
        [$originalClass, $method] = explode('::', $originalController, 2);
        
        // Descomponer el namespace del controlador original
        $classParts = explode('\\', $originalClass);
        
        if (count($classParts) < 3) {
            return $originalController; // Estructura no válida
        }
        
        // Extraer información base
        $baseNamespace = $classParts[0] . '\\' . $classParts[1]; // App\Controller
        $controllerType = $classParts[2] ?? 'Default'; // Dashboard, Mantenedores, etc.
        $controllerName = end($classParts); // DefaultController, PacientesController, etc.
        
        $tenantKey = ucfirst($tenantSubdomain);
        
        // Generar patrones de búsqueda dinámicos
        $dynamicPatterns = [
            // 1. Inyectar tenant en la posición 3 (después de Controller)
            // App\Controller\Dashboard\Melisahospital\DefaultController
            "{$baseNamespace}\\{$controllerType}\\{$tenantKey}\\{$controllerName}",
            
            // 2. Reemplazar la posición 3 completamente por el tenant
            // App\Controller\Melisahospital\DefaultController
            "{$baseNamespace}\\{$tenantKey}\\{$controllerName}",
            
            // 3. Mantener estructura pero cambiar a Default si el tenant no existe
            // App\Controller\Dashboard\Default\DefaultController
            "{$baseNamespace}\\{$controllerType}\\Default\\{$controllerName}",
            
            // 4. Controlador original sin modificar
            $originalClass
        ];
        
        // Buscar el primer patrón que funcione
        foreach ($dynamicPatterns as $pattern) {
            if (class_exists($pattern) && method_exists($pattern, $method)) {
                $this->logger->debug('Controlador resuelto dinámicamente desde ruta', [
                    'original' => $originalController,
                    'tenant' => $tenantSubdomain,
                    'resolved' => $pattern . '::' . $method,
                    'pattern' => $pattern
                ]);
                
                return $pattern . '::' . $method;
            }
        }
        
        $this->logger->warning('No se pudo resolver controlador dinámico desde ruta', [
            'original' => $originalController,
            'tenant' => $tenantSubdomain,
            'patterns_tried' => $dynamicPatterns
        ]);
        
        // Fallback al controlador original
        return $originalController;
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
            'dashboard_controller_exists' => $this->controllerExistsForTenant($subdomain, 'dashboard'),
        ];
    }
}