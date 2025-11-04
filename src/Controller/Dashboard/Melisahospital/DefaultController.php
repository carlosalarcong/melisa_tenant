<?php

namespace App\Controller\Dashboard\Melisahospital;

use App\Controller\AbstractTenantAwareController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Dashboard específico de Melisa Hospital - completamente transparente
 * No requiere inyectar nada en el constructor
 * El tenant está disponible automáticamente vía $this->tenant
 */
class DefaultController extends AbstractTenantAwareController
{
    #[Route('/dashboard', name: 'app_dashboard_melisahospital')]
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
            'tenant_name' => $this->getTenantName(),
            'subdomain' => $this->getTenantSubdomain(),
            'logged_user' => $loggedUser,
            'menu_routes' => $menuRoutes,
            'page_title' => 'Dashboard - ' . $this->getTenantName(),
            'current_locale' => $request->getLocale()
        ]);
    }
}