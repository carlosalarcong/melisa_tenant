<?php

namespace App\Service\Menu;

use App\Repository\Tenant\TenantPermissionProfileRepository;

/**
 * Factory para crear la estrategia de permisos apropiada según el perfil del tenant.
 */
class PermissionStrategyFactory
{
    public function __construct(
        private TenantPermissionProfileRepository $profileRepository,
        private CollaborativePermissionStrategy $collaborativeStrategy,
        private RestrictivePermissionStrategy $restrictiveStrategy,
        private CustomPermissionStrategy $customStrategy
    ) {}

    /**
     * Crea la estrategia apropiada basada en el perfil del tenant.
     *
     * @return PermissionStrategyInterface
     */
    public function createStrategy(): PermissionStrategyInterface
    {
        $profile = $this->profileRepository->getCurrentProfile();
        
        // Si no hay perfil configurado, usar collaborative por defecto
        if (!$profile) {
            return $this->collaborativeStrategy;
        }
        
        return match ($profile->getProfileType()) {
            'collaborative' => $this->collaborativeStrategy,
            'restrictive' => $this->restrictiveStrategy,
            'custom' => $this->customStrategy,
            default => $this->collaborativeStrategy,
        };
    }
}
