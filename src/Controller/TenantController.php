<?php

namespace App\Controller;

use App\Service\DynamicControllerResolver;
use App\Service\TenantContext;
use App\Entity\Tenant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantController extends AbstractController
{
    public function __construct(
        private DynamicControllerResolver $controllerResolver,
        private TenantContext $tenantContext
    ) {}
    
    public function handleDynamicRequest(
        Request $request, 
        string $controller = 'dashboard', 
        string $action = 'index'
    ): Response {
        // Verificar cookies de "recordarme" si no hay sesión
        $this->checkRememberMeCookies($request);
        
        // Verificar si el usuario está logueado
        $session = $request->getSession();
        if (!$session->get('logged_in')) {
            return $this->redirectToRoute('app_login');
        }
        
        // Obtener tenant desde la sesión
        $tenantData = $session->get('tenant');
        $tenant = $this->createTenantFromSession($tenantData);
        $this->tenantContext->setCurrentTenant($tenant);
        
        if (!$tenant) {
            throw $this->createNotFoundException('Tenant not found');
        }
        
        $subdomain = $tenant->getSubdomain();
        $controllerAction = $this->controllerResolver->resolveController($subdomain, $controller, $action);
        
        // Ejecutar el controlador dinámicamente
        return $this->forward($controllerAction, [
            'request' => $request,
            'tenant' => $tenant
        ]);
    }
    
    public function handleRootRequest(Request $request): Response 
    {
        return $this->handleDynamicRequest($request, 'dashboard', 'index');
    }
    
    private function createTenantFromSession(array $tenantData): Tenant
    {
        $tenant = new Tenant();
        // No establecer ID porque es auto-generado en la entidad
        $tenant->setName($tenantData['name']);
        $tenant->setSubdomain($tenantData['subdomain']);
        $tenant->setDatabaseName($tenantData['database_name'] ?? 'tenant_' . $tenantData['subdomain']);
        $tenant->setIsActive(true);
        $tenant->setStatus('active');
        $tenant->setPlan('basic');
        
        return $tenant;
    }
    
    private function checkRememberMeCookies(Request $request): void
    {
        $session = $request->getSession();
        
        // Si ya está logueado, no verificar cookies
        if ($session->get('logged_in')) {
            return;
        }
        
        // Verificar si existen cookies de "recordarme"
        $tenantCookie = $request->cookies->get('remember_tenant');
        $memberCookie = $request->cookies->get('remember_member');
        $tokenCookie = $request->cookies->get('remember_token');
        
        if ($tenantCookie && $memberCookie && $tokenCookie) {
            // Verificar que el token sea válido
            $expectedToken = hash('sha256', $tenantCookie . $memberCookie . 'melisa_secret');
            
            if (hash_equals($expectedToken, $tokenCookie)) {
                // Restaurar sesión desde cookies
                $tenant = json_decode(base64_decode($tenantCookie), true);
                $member = json_decode(base64_decode($memberCookie), true);
                
                if ($tenant && $member) {
                    $session->set('logged_in', true);
                    $session->set('tenant', $tenant);
                    $session->set('member', $member);
                }
            }
        }
    }
    
    private function createMockTenant(Request $request): ?Tenant
    {
        // Simulamos detección de tenant basado en host
        $host = $request->getHost();
        $subdomain = $this->tenantContext->extractSubdomain($host);
        
        // Si no hay subdominio, usar uno por defecto para testing
        if (!$subdomain) {
            $subdomain = 'clinica1'; // Default para testing
        }
        
        $tenant = new Tenant();
        $tenant->setId(1);
        $tenant->setName(ucfirst($subdomain) . ' Medical Center');
        $tenant->setSubdomain($subdomain);
        $tenant->setDatabaseName('tenant_' . $subdomain);
        $tenant->setIsActive(true);
        
        return $tenant;
    }
}
