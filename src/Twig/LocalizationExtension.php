<?php

namespace App\Twig;

use App\Service\LocalizationService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

/**
 * Extensión Twig para funcionalidades de localización en Melisa Tenant
 * 
 * Proporciona filtros y funciones para traducciones específicas por tenant
 */
class LocalizationExtension extends AbstractExtension
{
    private LocalizationService $localizationService;

    public function __construct(LocalizationService $localizationService)
    {
        $this->localizationService = $localizationService;
    }

    public function getFilters(): array
    {
        return [
            // Filtro para traducciones por tenant: {{ 'auth.login'|ttrans }}
            new TwigFilter('ttrans', [$this, 'translateTenant']),
            // Alias para mantener compatibilidad
            new TwigFilter('tenant_trans', [$this, 'translateTenant']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_locale', [$this, 'getCurrentLocale']),
            new TwigFunction('supported_locales', [$this, 'getSupportedLocales']),
            new TwigFunction('tenant_trans', [$this, 'getTenantTranslations']),
            new TwigFunction('locale_name', [$this, 'getCurrentLocaleName']),
            new TwigFunction('locale_flag', [$this, 'getLocaleFlag']),
            // Función de traducción también
            new TwigFunction('ttrans', [$this, 'translateTenant']),
        ];
    }

    /**
     * Traduce usando el dominio del tenant automáticamente
     * 
     * Uso en Twig: {{ 'auth.login'|ttrans }}
     *              {{ 'auth.user_not_found'|ttrans({'%tenant%': 'Hospital'}) }}
     */
    public function translateTenant(string $id, array $parameters = []): string
    {
        return $this->localizationService->trans($id, $parameters);
    }

    /**
     * Obtiene el idioma actual
     */
    public function getCurrentLocale(): string
    {
        return $this->localizationService->getCurrentLocale();
    }

    /**
     * Obtiene todos los idiomas soportados con su información
     */
    public function getSupportedLocales(): array
    {
        return $this->localizationService->getSupportedLocalesInfo();
    }

    /**
     * Obtiene traducciones específicas del tenant
     */
    public function getTenantTranslations(): array
    {
        return $this->localizationService->getTenantSpecificTranslations();
    }

    /**
     * Obtiene el nombre del idioma actual
     */
    public function getCurrentLocaleName(): string
    {
        return $this->localizationService->getCurrentLocaleName();
    }

    /**
     * Obtiene la bandera del idioma especificado
     */
    public function getLocaleFlag(string $locale): string
    {
        $locales = $this->localizationService->getSupportedLocalesInfo();
        return $locales[$locale]['flag'] ?? '🌐';
    }
}