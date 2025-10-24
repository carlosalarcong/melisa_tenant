<?php

namespace App\Controller\Dashboard\Melisalacolina;

use App\Service\RouteResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private RouteResolver $routeResolver
    ) {}

    #[Route('/dashboard', name: 'app_dashboard_melisalacolina')]
    public function index(): Response
    {
        $session = $this->container->get('request_stack')->getCurrentRequest()->getSession();
        $tenantData = $session->get('tenant_data', []);
        $userData = $session->get('user_data', []);
        
        // Generar menú dinámico usando RouteResolver
        $tenantSubdomain = $tenantData['subdomain'] ?? 'melisalacolina';
        $menuRoutes = [
            'dashboard' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_dashboard'),
            'pacientes' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_pacientes'),
            'citas' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_citas'),
            'mantenedores' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_mantenedores'),
            'reportes' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_reportes'),
            'configuracion' => $this->routeResolver->resolveRoute($tenantSubdomain, 'app_configuracion'),
        ];
        
        // Resolver plantilla dinámicamente usando RouteResolver
        $template = $this->routeResolver->resolveTemplate($tenantSubdomain, 'dashboard');
        
        return $this->render($template, [
            'tenant' => $tenantData,
            'tenant_name' => $tenantData['name'] ?? 'Clínica La Colina',
            'subdomain' => $tenantData['subdomain'] ?? 'melisalacolina',
            'logged_user' => $userData,
            'menu_routes' => $menuRoutes,
            'page_title' => 'Dashboard - Clínica La Colina'
        ]);
    }
}