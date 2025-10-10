<?php

namespace App\Controller\Dashboard\Default;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard_default')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $tenantData = $session->get('tenant_data', []);
        $userData = $session->get('user_data', []);
        
        return $this->render('dashboard/default/index.html.twig', [
            'tenant' => $tenantData,
            'tenant_name' => $tenantData['name'] ?? 'Melisa Clinic',
            'subdomain' => $tenantData['subdomain'] ?? 'melisawiclinic',
            'logged_user' => $userData,
            'page_title' => 'Dashboard - Melisa Clinic'
        ]);
    }
}