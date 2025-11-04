<?php

namespace App\Controller\Dashboard\Default;

use App\Controller\AbstractTenantAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Dashboard por defecto - completamente transparente
 * No requiere inyectar nada en el constructor
 * El tenant está disponible automáticamente vía $this->tenant
 */
class DefaultController extends AbstractTenantAwareController
{
    #[Route('/dashboard', name: 'app_dashboard_default')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        
        // ✨ $this->tenant está disponible automáticamente - inyectado por TenantContextInjector
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
        
        // Menú básico para template default
        $menuRoutes = [
            'dashboard' => ['url' => '/dashboard', 'label' => 'Dashboard'],
            'pacientes' => ['url' => '/pacientes', 'label' => 'Pacientes'],
            'citas' => ['url' => '/citas', 'label' => 'Citas'],
            'mantenedores' => ['url' => '/mantenedores', 'label' => 'Mantenedores'],
            'reportes' => ['url' => '/reportes', 'label' => 'Reportes'],
            'configuracion' => ['url' => '/configuracion', 'label' => 'Configuración'],
        ];
        
        // Render directo del template default
        return $this->render('dashboard/default.html.twig', [
            'tenant' => $tenant,
            'tenant_name' => $this->getTenantName(),
            'subdomain' => $this->getTenantSubdomain(),
            'logged_user' => $loggedUser,
            'menu_routes' => $menuRoutes,
            'page_title' => 'Dashboard - ' . $this->getTenantName(),
            'current_locale' => $request->getLocale()
        ]);
    }
}