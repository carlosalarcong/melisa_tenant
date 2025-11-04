<?php

// Script de prueba para verificar traducciones por tenant
// Ejecutar desde: php bin/console debug:router app_test_translation_direct

namespace App\Controller;

use App\Service\LocalizationService;
use App\Service\TenantResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TranslationDebugController extends AbstractController
{
    public function __construct(
        private LocalizationService $localizationService,
        private TenantResolver $tenantResolver
    ) {}

    #[Route('/debug/translation', name: 'app_test_translation_direct')]
    public function debug(Request $request): Response
    {
        // Obtener tenant actual
        $tenant = $this->tenantResolver->resolveTenantFromRequest($request);
        
        // Probar traducción directa
        $translation = $this->localizationService->trans('auth.login');
        
        $info = [
            'tenant' => $tenant ? $tenant['subdomain'] : 'NO TENANT',
            'locale' => $this->localizationService->getCurrentLocale(),
            'translation_key' => 'auth.login',
            'translated_value' => $translation,
            'expected_hospital' => 'Ingreso al Sistema Hospitalario',
            'expected_clinic' => 'Acceso a La Colina',
            'expected_default' => 'Acceso al Sistema',
        ];
        
        return new Response('<pre>' . print_r($info, true) . '</pre>');
    }
}
