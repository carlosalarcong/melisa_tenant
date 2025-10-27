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
        
        // Menú específico para hospital
        $menuRoutes = [
            'dashboard' => ['url' => '/dashboard', 'label' => 'Dashboard'],
            'pacientes' => ['url' => '/pacientes', 'label' => 'Pacientes'],
            'citas' => ['url' => '/citas', 'label' => 'Citas'],
            'quirofanos' => ['url' => '/quirofanos', 'label' => 'Quirófanos'],
            'hospitalizacion' => ['url' => '/hospitalizacion', 'label' => 'Hospitalización'],
            'laboratorio' => ['url' => '/laboratorio', 'label' => 'Laboratorio'],
            'farmacia' => ['url' => '/farmacia', 'label' => 'Farmacia'],
            'mantenedores' => ['url' => '/mantenedores', 'label' => 'Mantenedores'],
            'reportes' => ['url' => '/reportes', 'label' => 'Reportes'],
            'configuracion' => ['url' => '/configuracion', 'label' => 'Configuración'],
        ];
        
        // Render directo del template específico de melisahospital
        return $this->render('dashboard/melisahospital/index.html.twig', [
            'tenant' => $tenant,
            'tenant_name' => $tenant['name'],
            'subdomain' => $tenant['subdomain'],
            'logged_user' => $loggedUser,
            'menu_routes' => $menuRoutes,
            'page_title' => $this->localizationService->trans('dashboard.title') . ' - ' . $tenant['name'],
            'current_locale' => $request->getLocale()
        ]);
    }
}