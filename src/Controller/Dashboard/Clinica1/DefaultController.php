<?php

namespace App\Controller\Dashboard\Clinica1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function index(Request $request, ?Tenant $tenant = null): Response
    {
        $session = $request->getSession();
        $tenantData = $session->get('tenant');
        $memberData = $session->get('member');
        
        return $this->render('dashboard/clinica1.html.twig', [
            'controller_name' => 'Dashboard\\Clinica1\\DefaultController',
            'message' => 'Hola Mundo estas en Clinica1',
            'tenant' => $tenantData,
            'member' => $memberData,
            'tenant_name' => $tenantData['name'] ?? 'Clínica 1',
            'subdomain' => $tenantData['subdomain'] ?? 'clinica1',
            'logged_user' => $memberData
        ]);
    }
    
    public function dashboard(Request $request, ?Tenant $tenant = null): Response
    {
        return $this->index($request, $tenant);
    }
}