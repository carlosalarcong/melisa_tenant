<?php

namespace App\EventListener;

use App\Service\TenantContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\LocaleAwareInterface;

/**
 * Listener que establece el dominio de traducción basado en el tenant
 * 
 * Este listener configura el locale Y el dominio de traducción específico del tenant
 * ANTES de que se ejecuten los controllers, permitiendo que TranslatorInterface
 * busque las traducciones en la carpeta correcta.
 * 
 * Priority: 25 (ejecuta ANTES de LocaleListener que tiene priority 20)
 */
class TenantTranslationListener implements EventSubscriberInterface
{
    private TenantContext $tenantContext;
    private string $projectDir;
    
    public function __construct(
        TenantContext $tenantContext,
        string $projectDir
    ) {
        $this->tenantContext = $tenantContext;
        $this->projectDir = $projectDir;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        
        // Determinar el subdomain del tenant
        $tenantSubdomain = 'default';
        
        if ($this->tenantContext->hasCurrentTenant()) {
            $tenant = $this->tenantContext->getCurrentTenant();
            $tenantSubdomain = $tenant['subdomain'] ?? 'default';
        }
        
        // Establecer el tenant subdomain en atributos del request
        // Esto permite que el TranslationLoader lo lea después
        $request->attributes->set('_tenant_subdomain', $tenantSubdomain);
        $request->attributes->set('_tenant_translation_path', $this->getTenantTranslationPath($tenantSubdomain));
    }

    /**
     * Obtiene el path de traducciones específico del tenant
     */
    private function getTenantTranslationPath(string $tenantSubdomain): string
    {
        $tenantPath = $this->projectDir . '/translations/' . $tenantSubdomain;
        
        // Si la carpeta del tenant existe, usar esa; sino usar default
        if (is_dir($tenantPath)) {
            return $tenantPath;
        }
        
        return $this->projectDir . '/translations/default';
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Priority 25: Ejecuta ANTES de LocaleListener (20)
            KernelEvents::REQUEST => [['onKernelRequest', 25]],
        ];
    }
}
