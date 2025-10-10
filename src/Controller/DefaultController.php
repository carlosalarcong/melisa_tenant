<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\TenantResolver;
use App\Service\TenantContext;

class DefaultController extends AbstractController
{
    private TenantResolver $tenantResolver;
    private TenantContext $tenantContext;

    public function __construct(TenantResolver $tenantResolver, TenantContext $tenantContext)
    {
        $this->tenantResolver = $tenantResolver;
        $this->tenantContext = $tenantContext;
    }

    public function index(Request $request): Response
    {
        // Obtener información del tenant actual
        $tenantData = $this->tenantContext->getCurrentTenant();
        
        if (!$tenantData) {
            // Si no hay tenant, redirigir al login
            return $this->redirectToRoute('app_login');
        }

        // Redirigir al dashboard del tenant
        return $this->redirectToRoute('app_dashboard', ['controller' => 'dashboard']);
    }

    public function handleDynamicRequest(Request $request): Response
    {
        $controller = $request->attributes->get('controller', 'dashboard');
        $action = $request->attributes->get('action', 'index');
        
        // Obtener información del tenant actual
        $tenantData = $this->tenantContext->getCurrentTenant();
        
        if (!$tenantData) {
            return $this->redirectToRoute('app_login');
        }

        // Intentar cargar controlador específico del tenant
        $tenantController = $this->tryLoadTenantController($tenantData['subdomain'], $controller);
        
        if ($tenantController) {
            return $this->forward($tenantController . '::' . $action, $request->query->all());
        }

        // Fallback a dashboard por defecto
        return $this->render('dashboard/default.html.twig', [
            'tenant_name' => $tenantData['name'] ?? 'Sistema Médico',
            'subdomain' => $tenantData['subdomain'] ?? '',
            'controller' => $controller,
            'action' => $action
        ]);
    }

    private function tryLoadTenantController(string $subdomain, string $controller): ?string
    {
        // Intentar cargar controlador específico por tenant
        $controllerClass = 'App\\Controller\\Dashboard\\' . ucfirst($subdomain) . '\\DefaultController';
        
        if (class_exists($controllerClass)) {
            return $controllerClass;
        }

        // Fallback a controlador por defecto
        $defaultControllerClass = 'App\\Controller\\Dashboard\\Default\\DefaultController';
        
        if (class_exists($defaultControllerClass)) {
            return $defaultControllerClass;
        }

        return null;
    }
}
