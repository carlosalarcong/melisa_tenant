<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\TenantContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * State Provider dinámico que delega a State Providers específicos por tenant
 * 
 * Similar al DynamicControllerResolver pero para API Platform State Providers
 */
class DynamicPatientStateProvider implements ProviderInterface
{
    public function __construct(
        private TenantContext $tenantContext,
        private RequestStack $requestStack
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Obtener tenant actual
        $request = $this->requestStack->getCurrentRequest();
        $tenant = $request?->headers->get('X-Tenant-Context') ?? 'melisahospital';
        
        // Determinar el State Provider específico del tenant
        $providerClass = $this->getProviderClassForTenant($tenant);
        
        // Si existe un State Provider específico, usarlo
        if (class_exists($providerClass)) {
            $provider = new $providerClass($this->tenantContext, $this->requestStack);
            return $provider->provide($operation, $uriVariables, $context);
        }
        
        // Fallback al State Provider por defecto
        $defaultProvider = new \App\State\Default\PatientStateProvider($this->tenantContext, $this->requestStack);
        return $defaultProvider->provide($operation, $uriVariables, $context);
    }

    /**
     * Determina la clase del State Provider según el tenant
     */
    private function getProviderClassForTenant(string $tenant): string
    {
        // Mapeo de tenants a namespaces específicos
        $tenantClassMap = [
            'melisahospital' => 'App\\State\\Default\\PatientStateProvider',
            'melisalacolina' => 'App\\State\\Melisalacolina\\PatientStateProvider',
            'melisawiclinic' => 'App\\State\\Melisawiclinic\\PatientStateProvider',
        ];

        return $tenantClassMap[$tenant] ?? 'App\\State\\Default\\PatientStateProvider';
    }
}