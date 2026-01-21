<?php

namespace App\Service\Menu;

use App\Repository\Tenant\TenantModulePermissionOverrideRepository;

/**
 * Estrategia Custom: Lee permisos desde la base de datos.
 * Permite configuración personalizada por tenant.
 */
class CustomPermissionStrategy implements PermissionStrategyInterface
{
    public function __construct(
        private TenantModulePermissionOverrideRepository $overrideRepository
    ) {}

    public function canAccess(string $moduleName, array $userRoles): bool
    {
        // Obtener los roles requeridos desde la base de datos
        $requiredRoles = $this->overrideRepository->getRequiredRolesForModule($moduleName);
        
        // Si no hay override definido, solo ADMIN puede acceder
        if (empty($requiredRoles)) {
            return in_array('ROLE_ADMIN', $userRoles);
        }
        
        // Verificar si el usuario tiene alguno de los roles requeridos
        return !empty(array_intersect($userRoles, $requiredRoles));
    }

    public function getType(): string
    {
        return 'custom';
    }
}
