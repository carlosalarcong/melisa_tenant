<?php

namespace App\Controller\Dashboard\Melisahospital;

use App\Service\LocalizationService;
use App\Service\TenantContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private LocalizationService $localizationService;
    private TenantContext $tenantContext;

    public function __construct(
        LocalizationService $localizationService,
        TenantContext $tenantContext
    ) {
        $this->localizationService = $localizationService;
        $this->tenantContext = $tenantContext;
    }

        #[Route('/dashboard', name: 'app_dashboard_melisahospital')]
    public function index(Request $request): Response
    {
        // Establecer idioma actual
        $locale = $this->localizationService->getCurrentLocale();
        $request->setLocale($locale);
        
        // Obtener datos del tenant y usuario desde el contexto
        $tenant = $this->tenantContext->getCurrentTenant();
        $session = $request->getSession();
        
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
        
        return $this->render('dashboard/melisahospital/index.html.twig', [
            'tenant' => $tenant,
            'tenant_name' => $tenant['name'] ?? 'Hospital',
            'subdomain' => $tenant['subdomain'] ?? 'melisahospital',
            'logged_user' => $loggedUser,
            'page_title' => $this->localizationService->trans('dashboard.title') . ' - ' . $this->localizationService->trans('establishments.hospital'),
            'current_locale' => $locale
        ]);
    }
}