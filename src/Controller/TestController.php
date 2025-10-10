<?php

namespace App\Controller;

use App\Service\LocalizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    private LocalizationService $localizationService;

    public function __construct(LocalizationService $localizationService)
    {
        $this->localizationService = $localizationService;
    }

    #[Route('/test/translations', name: 'test_translations')]
    public function testTranslations(Request $request): JsonResponse
    {
        $locale = $this->localizationService->getCurrentLocale();
        
        return new JsonResponse([
            'current_locale' => $locale,
            'request_locale' => $request->getLocale(),
            'translations' => [
                'dashboard_title' => $this->localizationService->trans('dashboard.title'),
                'hospital_emergencies' => $this->localizationService->trans('hospital.active_emergencies'),
                'establishments_hospital' => $this->localizationService->trans('establishments.hospital'),
                'auth_login' => $this->localizationService->trans('auth.login'),
            ],
            'session_locale' => $request->getSession()->get('_locale'),
            'tenant_translations' => $this->localizationService->getTenantSpecificTranslations()
        ]);
    }
}