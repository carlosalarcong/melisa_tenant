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
    private TenantResolver $tenantResolver;
    
    private array $supportedLocales = ['es', 'en'];
    private string $defaultLocale = 'es';

    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        TenantContext $tenantContext,
        TenantResolver $tenantResolver
    ) {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->tenantContext = $tenantContext;
        $this->tenantResolver = $tenantResolver;
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

        // Para rutas API, usar locale simplificado sin sesiones
        $pathInfo = $request->getPathInfo();
        if (str_starts_with($pathInfo, '/api/')) {
            // Para API, usar header Accept-Language o default
            $preferredLanguage = $request->getPreferredLanguage($this->supportedLocales);
            return $preferredLanguage ?? $this->defaultLocale;
        }

        // Para rutas web normales, usar lógica completa con sesiones
        
        // 1. Prioridad: Parámetro en sesión del usuario
        if ($request->hasSession()) {
            $session = $request->getSession();
            if ($session->has('_locale')) {
                $locale = $session->get('_locale');
                if (in_array($locale, $this->supportedLocales)) {
                    return $locale;
                }
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
     * Traduce un mensaje en el idioma actual usando el dominio del tenant
     * 
     * Este método busca traducciones en este orden:
     * 1. Dominio específico del tenant (melisahospital, melisalacolina, etc.)
     * 2. Dominio messages global (fallback)
     * 
     * Usa el método trans() del TranslatorInterface que retorna:
     * - La traducción si la encuentra
     * - La clave original si NO la encuentra
     */
    public function trans(string $id, array $parameters = [], string $domain = 'messages'): string
    {
        $locale = $this->getCurrentLocale();
        $tenantDomain = $this->getTenantDomain();
        
        // Debug: agregar logging temporal
        // dump(['id' => $id, 'tenant_domain' => $tenantDomain, 'locale' => $locale]);
        
        // Si el dominio del tenant NO es 'default' ni 'messages', buscar allí primero
        if ($tenantDomain !== 'default' && $tenantDomain !== 'messages') {
            $tenantTranslation = $this->translator->trans($id, $parameters, $tenantDomain, $locale);
            
            // Si encontró la traducción (es diferente a la clave), retornarla
            if ($tenantTranslation !== $id) {
                return $tenantTranslation;
            }
        }
        
        // FALLBACK 1: Intentar en dominio 'default'
        if ($tenantDomain !== 'default') {
            $defaultTranslation = $this->translator->trans($id, $parameters, 'default', $locale);
            if ($defaultTranslation !== $id) {
                return $defaultTranslation;
            }
        }
        
        // FALLBACK 2: Usar dominio 'messages' estándar
        return $this->translator->trans($id, $parameters, 'messages', $locale);
    }
    
    /**
     * Obtiene el dominio de traducción específico del tenant
     * 
     * Detecta el tenant desde múltiples fuentes:
     * 1. TenantContext (si ya está establecido)
     * 2. TenantResolver desde el request actual
     * 
     * Por ejemplo:
     * - melisahospital → dominio: "melisahospital"
     * - melisalacolina → dominio: "melisalacolina"
     * - default → dominio: "default"
     */
    private function getTenantDomain(): string
    {
        // PRIORIDAD 1: TenantContext (ya establecido en sesión/request)
        if ($this->tenantContext->hasCurrentTenant()) {
            $tenant = $this->tenantContext->getCurrentTenant();
            $subdomain = $tenant['subdomain'] ?? 'default';
            return $subdomain;
        }
        
        // PRIORIDAD 2: Resolver desde el request actual (para login, etc.)
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            try {
                $tenant = $this->tenantResolver->resolveTenantFromRequest($request);
                if ($tenant && isset($tenant['subdomain'])) {
                    return $tenant['subdomain'];
                }
            } catch (\Exception $e) {
                // Si falla la resolución, usar default
            }
        }
        
        // FALLBACK: Usar dominio default
        return 'default';
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