<?php

namespace App\Controller\Dashboard\Melisahospital;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\LocalizationService;
use App\Service\TenantContext;
use App\Service\DynamicControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class DefaultController extends AbstractDashboardController
{
    private LocalizationService $localizationService;
    private TenantContext $tenantContext;

    public function __construct(
        LocalizationService $localizationService, // Maneja traducciones y configuración de idioma
        TenantContext $tenantContext, // Proporciona contexto actual del tenant logueado
        DynamicControllerResolver $controllerResolver, // Resuelve controladores específicos por tenant
        Environment $twig // Verifica existencia de plantillas antes de renderizar
    ) {
        parent::__construct($controllerResolver, $twig);
        $this->localizationService = $localizationService;
        $this->tenantContext = $tenantContext;
    }

    #[Route('/dashboard', name: 'app_dashboard_melisahospital')]
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
        
        $tenantSubdomain = $tenant['subdomain'] ?? 'melisahospital';
        
        // Usar el método helper de la clase base
        return $this->renderDashboard($tenantSubdomain, [
            'tenant' => $tenant,
            'tenant_name' => $tenant['name'],
            'subdomain' => $tenant['subdomain'],
            'logged_user' => $loggedUser,
            'page_title' => $this->localizationService->trans('dashboard.title') . ' - ' . $tenant['name'],
            'current_locale' => $request->getLocale()
        ]);
    }

    /**
     * Construye el menú dinámico específico para Melisahospital
     * Extiende el menú base con funcionalidades hospitalarias
     */
    protected function buildDynamicMenu(string $tenantSubdomain): array
    {
        $baseMenu = parent::buildDynamicMenu($tenantSubdomain);

        // Funcionalidades específicas del hospital
        $hospitalSpecific = [
            'quirofanos' => ['url' => '/quirofanos', 'label' => 'Quirófanos'],
            'hospitalizacion' => ['url' => '/hospitalizacion', 'label' => 'Hospitalización'],
            'laboratorio' => ['url' => '/laboratorio', 'label' => 'Laboratorio'],
            'farmacia' => ['url' => '/farmacia', 'label' => 'Farmacia'],
        ];

        return array_merge($baseMenu, $hospitalSpecific);
    }
}