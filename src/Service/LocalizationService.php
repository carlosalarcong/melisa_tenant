<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Servicio de localización para Melisa Tenant
 * Maneja el idioma por tenant y usuario
 */
class LocalizationService
{
    private TranslatorInterface $translator;
    private RequestStack $requestStack;
    private TenantContext $tenantContext;
    
    private array $supportedLocales = ['es', 'en'];
    private string $defaultLocale = 'es';

    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        TenantContext $tenantContext
    ) {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->tenantContext = $tenantContext;
    }

    /**
     * Obtiene el idioma actual basado en el tenant y usuario
     */
    public function getCurrentLocale(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        
        if (!$request) {
            return $this->defaultLocale;
        }

        // 1. Prioridad: Parámetro en sesión del usuario
        $session = $request->getSession();
        if ($session->has('_locale')) {
            $locale = $session->get('_locale');
            if (in_array($locale, $this->supportedLocales)) {
                return $locale;
            }
        }

        // 2. Prioridad: Configuración específica del tenant
        if ($this->tenantContext->hasCurrentTenant()) {
            $tenant = $this->tenantContext->getCurrentTenant();
            if (isset($tenant['locale']) && in_array($tenant['locale'], $this->supportedLocales)) {
                return $tenant['locale'];
            }
        }

        // 3. Prioridad: Header Accept-Language del navegador
        $preferredLanguage = $request->getPreferredLanguage($this->supportedLocales);
        if ($preferredLanguage) {
            return $preferredLanguage;
        }

        // 4. Fallback: Idioma por defecto
        return $this->defaultLocale;
    }

    /**
     * Establece el idioma para el usuario actual
     */
    public function setUserLocale(string $locale): bool
    {
        if (!in_array($locale, $this->supportedLocales)) {
            return false;
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
            return true;
        }

        return false;
    }

    /**
     * Traduce un mensaje en el idioma actual
     */
    public function trans(string $id, array $parameters = [], string $domain = 'messages'): string
    {
        return $this->translator->trans($id, $parameters, $domain, $this->getCurrentLocale());
    }

    /**
     * Obtiene todos los idiomas soportados
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    /**
     * Obtiene la información completa de idiomas soportados
     */
    public function getSupportedLocalesInfo(): array
    {
        return [
            'es' => [
                'code' => 'es',
                'name' => 'Español',
                'flag' => '🇪🇸',
                'direction' => 'ltr'
            ],
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'flag' => '🇺🇸',
                'direction' => 'ltr'
            ]
        ];
    }

    /**
     * Verifica si un idioma está soportado
     */
    public function isLocaleSupported(string $locale): bool
    {
        return in_array($locale, $this->supportedLocales);
    }

    /**
     * Obtiene el nombre del idioma actual
     */
    public function getCurrentLocaleName(): string
    {
        $locale = $this->getCurrentLocale();
        $localesInfo = $this->getSupportedLocalesInfo();
        
        return $localesInfo[$locale]['name'] ?? $locale;
    }

    /**
     * Obtiene configuraciones específicas del tenant para traducciones
     */
    public function getTenantSpecificTranslations(): array
    {
        if (!$this->tenantContext->hasCurrentTenant()) {
            return [];
        }

        $tenant = $this->tenantContext->getCurrentTenant();
        $tenantName = $tenant['subdomain'] ?? 'default';

        // Traducciones específicas por tipo de establecimiento
        $tenantTranslations = [
            'melisahospital' => [
                'es' => [
                    'establishment_type' => 'Hospital',
                    'welcome_message' => 'Bienvenido al Sistema Hospitalario',
                    'main_service' => 'Atención Hospitalaria'
                ],
                'en' => [
                    'establishment_type' => 'Hospital',
                    'welcome_message' => 'Welcome to the Hospital System',
                    'main_service' => 'Hospital Care'
                ]
            ],
            'melisalacolina' => [
                'es' => [
                    'establishment_type' => 'Clínica',
                    'welcome_message' => 'Bienvenido a La Colina',
                    'main_service' => 'Atención Clínica Especializada'
                ],
                'en' => [
                    'establishment_type' => 'Clinic',
                    'welcome_message' => 'Welcome to La Colina',
                    'main_service' => 'Specialized Clinical Care'
                ]
            ],
            'melisawiclinic' => [
                'es' => [
                    'establishment_type' => 'Centro Médico',
                    'welcome_message' => 'Bienvenido a Wi Clinic',
                    'main_service' => 'Tecnología Médica Avanzada'
                ],
                'en' => [
                    'establishment_type' => 'Medical Center',
                    'welcome_message' => 'Welcome to Wi Clinic',
                    'main_service' => 'Advanced Medical Technology'
                ]
            ]
        ];

        $currentLocale = $this->getCurrentLocale();
        
        return $tenantTranslations[$tenantName][$currentLocale] ?? [];
    }
}