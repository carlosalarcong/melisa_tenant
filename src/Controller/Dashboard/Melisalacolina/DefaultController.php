<?php

namespace App\Controller\Dashboard\Melisalacolina;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard_melisalacolina')]
    public function index(): Response
    {
        $session = $this->container->get('request_stack')->getCurrentRequest()->getSession();
        $tenantData = $session->get('tenant_data', []);
        $userData = $session->get('user_data', []);
        
        return $this->render('dashboard/melisalacolina/index.html.twig', [
            'tenant' => $tenantData,
            'tenant_name' => $tenantData['name'] ?? 'Clínica La Colina',
            'subdomain' => $tenantData['subdomain'] ?? 'melisalacolina',
            'logged_user' => $userData,
            'page_title' => 'Dashboard - Clínica La Colina'
        ]);
    }
}