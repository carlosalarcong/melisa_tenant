<?php

namespace App\Service;

use App\Entity\Tenant;

class DynamicControllerResolver
{
    public function resolveController(string $subdomain, string $controller, string $action = 'index'): string
    {
        // Para el dashboard, usar la estructura especial Dashboard/
        if ($controller === 'dashboard') {
            return $this->resolveDashboardController($subdomain, $action);
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
    
    public function controllerExists(string $subdomain, string $controller): bool
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
    
    public function getControllerPath(string $subdomain, string $controller): string
    {
        $tenantNamespace = ucfirst($subdomain);
        $controllerClass = ucfirst($controller) . 'Controller';
        
        return sprintf('App\\Controller\\%s\\%s', $tenantNamespace, $controllerClass);
    }
}