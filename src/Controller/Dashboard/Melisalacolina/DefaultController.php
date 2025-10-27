<?php

namespace App\Controller\Dashboard\Melisalacolina;

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

    #[Route('/dashboard', name: 'app_dashboard_melisalacolina')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        
        // Obtener datos del tenant usando método centralizado
        $tenant = $this->getTenantData();
        
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
        
        $tenantSubdomain = $tenant['subdomain'] ?? 'melisalacolina';
        
        // Usar el método helper de la clase base
        return $this->renderDashboard($tenantSubdomain, [
            'tenant' => $tenant,
            'tenant_name' => $tenant['name'],
            'subdomain' => $tenant['subdomain'],
            'logged_user' => $loggedUser,
            'page_title' => 'Dashboard - ' . $tenant['name'],
            'current_locale' => $request->getLocale()
        ]);
    }

    /**
     * Construye el menú dinámico específico para Melisalacolina
     * Extiende el menú base con funcionalidades de clínica
     */
    protected function buildDynamicMenu(string $tenantSubdomain): array
    {
        $baseMenu = parent::buildDynamicMenu($tenantSubdomain);

        // Funcionalidades específicas de la clínica
        $clinicSpecific = [
            'consultas' => ['url' => '/consultas', 'label' => 'Consultas'],
            'procedimientos' => ['url' => '/procedimientos', 'label' => 'Procedimientos'],
            'laboratorio' => ['url' => '/laboratorio', 'label' => 'Laboratorio'],
            'imagenologia' => ['url' => '/imagenologia', 'label' => 'Imagenología'],
        ];

        return array_merge($baseMenu, $clinicSpecific);
    }
}