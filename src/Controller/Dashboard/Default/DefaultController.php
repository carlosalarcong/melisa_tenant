<?php

namespace App\Controller\Dashboard\Default;

use App\Controller\AbstractTenantAwareController;
use App\Service\Dashboard\DashboardMetricsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Dashboard moderno con métricas y widgets
 */
class DefaultController extends AbstractTenantAwareController
{
    public function __construct(
        private DashboardMetricsService $metricsService
    ) {}

    #[Route('/dashboard', name: 'app_dashboard_default')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        
        // Obtener tenant como array
        $tenant = $this->getTenant();
        
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

        // Obtener roles del usuario (desde sesión o user entity)
        // TODO: Adaptar según tu sistema de autenticación real
        $userRoles = $session->get('user_roles', []);
        
        // Si no hay roles en sesión, usar ROLE_ADMIN por defecto temporalmente
        // hasta que se implemente el sistema de roles real
        if (empty($userRoles)) {
            $userRoles = ['ROLE_ADMIN'];
        }
        
        // Obtener métricas del dashboard (filtradas por rol)
        $metrics = $this->metricsService->getDashboardMetrics($tenant, $userRoles);
        $modules = $this->metricsService->getAvailableModules($userRoles);
        
        // Render del template moderno
        return $this->render('dashboard/index.html.twig', [
            'tenant' => $tenant, // Array para el template
            'tenant_name' => $this->getTenantName(),
            'subdomain' => $this->getTenantSubdomain(),
            'logged_user' => $loggedUser,
            'user_roles' => $userRoles, // Enviar roles al template
            'metrics' => $metrics,
            'modules' => $modules,
            'page_title' => 'Dashboard - ' . $this->getTenantName(),
            'current_locale' => $request->getLocale()
        ]);
    }
}