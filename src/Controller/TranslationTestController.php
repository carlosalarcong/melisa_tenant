<?php

namespace App\Controller;

use App\Service\LocalizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller de prueba para demostrar traducciones específicas por tenant
 */
class TranslationTestController extends AbstractTenantAwareController
{
    public function __construct(
        private LocalizationService $localizationService,
        private TranslatorInterface $translator
    ) {}

    #[Route('/test/translations', name: 'app_test_translations')]
    public function testTranslations(): Response
    {
        $locale = $this->localizationService->getCurrentLocale();
        $tenantName = $this->getTenantName();
        
        // Probar traducciones con TranslatorInterface directo
        $translations = [
            'auth.login' => $this->translator->trans('auth.login', [], 'messages', $locale),
            'auth.logout' => $this->translator->trans('auth.logout', [], 'messages', $locale),
            'nav.dashboard' => $this->translator->trans('nav.dashboard', [], 'messages', $locale),
            'nav.patients' => $this->translator->trans('nav.patients', [], 'messages', $locale),
            'dashboard.title' => $this->translator->trans('dashboard.title', [], 'messages', $locale),
            'patients.title' => $this->translator->trans('patients.title', [], 'messages', $locale),
            'establishment.type' => $this->translator->trans('establishment.type', [], 'messages', $locale),
            'establishment.welcome_message' => $this->translator->trans('establishment.welcome_message', [], 'messages', $locale),
        ];

        return $this->render('test/translations.html.twig', [
            'tenant_name' => $tenantName,
            'tenant_subdomain' => $this->getTenantSubdomain(),
            'locale' => $locale,
            'translations' => $translations
        ]);
    }
}
