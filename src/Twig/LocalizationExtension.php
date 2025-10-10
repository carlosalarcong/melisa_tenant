<?php

namespace App\Twig;

use App\Service\LocalizationService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extensión Twig para funcionalidades de localización en Melisa Tenant
 */
class LocalizationExtension extends AbstractExtension
{
    private LocalizationService $localizationService;

    public function __construct(LocalizationService $localizationService)
    {
        $this->localizationService = $localizationService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_locale', [$this, 'getCurrentLocale']),
            new TwigFunction('supported_locales', [$this, 'getSupportedLocales']),
            new TwigFunction('tenant_trans', [$this, 'getTenantTranslations']),
            new TwigFunction('locale_name', [$this, 'getCurrentLocaleName']),
            new TwigFunction('locale_flag', [$this, 'getLocaleFlag']),
        ];
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