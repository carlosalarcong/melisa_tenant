<?php

namespace App\Controller\Dashboard\Default;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\DynamicControllerResolver;
use App\Service\TenantContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class DefaultController extends AbstractDashboardController
{
    private TenantContext $tenantContext;

    public function __construct(
        TenantContext $tenantContext, // Proporciona contexto actual del tenant logueado
        DynamicControllerResolver $controllerResolver, // Resuelve controladores específicos por tenant
        Environment $twig // Verifica existencia de plantillas antes de renderizar
    ) {
        parent::__construct($controllerResolver, $twig);
        $this->tenantContext = $tenantContext;
    }

    #[Route('/dashboard', name: 'app_dashboard_default')]
    public function index(Request $request): Response
    {
        // Obtener datos del tenant y usuario desde el contexto
        $tenant = $this->tenantContext->getCurrentTenant();
        $session = $request->getSession();
        
        // Obtener datos del usuario logueado
        $loggedUser = [
            'id' => $session->get('user_id'),
            'username' => $session->get('username'),
            'first_name' => $session->get('user_name', ''),
            'last_name' => '',
            'role' => 'Usuario'
        ];
        
        // Si user_name tiene nombre completo, separarlo
        $fullName = $session->get('user_name', '');
        if ($fullName) {
            $nameParts = explode(' ', $fullName, 2);
            $loggedUser['first_name'] = $nameParts[0] ?? '';
            $loggedUser['last_name'] = $nameParts[1] ?? '';
        }
        
        $tenantSubdomain = $tenant['subdomain'] ?? 'default';
        
        // Usar el método helper de la clase base
        return $this->renderDashboard($tenantSubdomain, [
            'tenant' => $tenant,
            'tenant_name' => $tenant['name'] ?? 'Melisa Clinic',
            'subdomain' => $tenant['subdomain'] ?? 'default',
            'logged_user' => $loggedUser,
            'page_title' => 'Dashboard - Melisa Clinic',
            'current_locale' => $request->getLocale()
        ]);
    }
}