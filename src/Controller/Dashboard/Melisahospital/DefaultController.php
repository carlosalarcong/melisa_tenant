<?php

namespace App\Controller\Dashboard\Melisahospital;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard_melisahospital')]
    public function index(): Response
    {
        $session = $this->container->get('request_stack')->getCurrentRequest()->getSession();
        $tenantData = $session->get('tenant_data', []);
        $userData = $session->get('user_data', []);
        
        return $this->render('dashboard/melisahospital/index.html.twig', [
            'tenant' => $tenantData,
            'tenant_name' => $tenantData['name'] ?? 'Hospital Central Melisa',
            'subdomain' => $tenantData['subdomain'] ?? 'melisahospital',
            'logged_user' => $userData,
            'page_title' => 'Dashboard - Hospital Central'
        ]);
    }
}