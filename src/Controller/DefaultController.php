<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractTenantController
{
    public function index(Request $request, ?Tenant $tenant = null): Response
    {
        return $this->renderWithTenant('dashboard/index.html.twig', [
            'controller_name' => 'DefaultController',
            'message' => 'Controlador genérico - No hay controlador personalizado para este tenant',
            'available_actions' => ['index', 'dashboard', 'pacientes', 'citas']
        ]);
    }
    
    public function dashboard(Request $request, ?Tenant $tenant = null): Response
    {
        return $this->renderWithTenant('dashboard/index.html.twig', [
            'controller_name' => 'DefaultController',
            'action' => 'dashboard',
            'message' => 'Dashboard genérico'
        ]);
    }
    
    public function pacientes(Request $request, ?Tenant $tenant = null): Response
    {
        return $this->renderWithTenant('pacientes/index.html.twig', [
            'controller_name' => 'DefaultController',
            'action' => 'pacientes',
            'message' => 'Módulo de pacientes genérico',
            'pacientes' => []
        ]);
    }
    
    public function citas(Request $request, ?Tenant $tenant = null): Response
    {
        return $this->renderWithTenant('citas/index.html.twig', [
            'controller_name' => 'DefaultController',
            'action' => 'citas',
            'message' => 'Módulo de citas genérico',
            'citas' => []
        ]);
    }
    
    public function notFound(Request $request, ?Tenant $tenant = null): Response
    {
        return $this->renderWithTenant('errors/404.html.twig', [
            'message' => 'Acción no encontrada en el controlador genérico'
        ], 404);
    }
}
