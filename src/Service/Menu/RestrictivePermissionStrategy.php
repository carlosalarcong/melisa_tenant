<?php

namespace App\Service\Menu;

/**
 * Estrategia Restrictive: Solo administradores pueden acceder.
 * Usada en clínicas pequeñas donde el dueño controla todo.
 */
class RestrictivePermissionStrategy implements PermissionStrategyInterface
{
    /**
     * Configuración restrictiva: solo ADMIN tiene acceso.
     * Algunos módulos básicos pueden ser accesibles para usuarios regulares.
     */
    private const DEFAULT_MODULE_PERMISSIONS = [
        'dashboard' => ['ROLE_ADMIN', 'ROLE_USER'],
        'patients' => ['ROLE_ADMIN'],
        'appointments' => ['ROLE_ADMIN'],
        'medical_records' => ['ROLE_ADMIN'],
        'reports' => ['ROLE_ADMIN'],
        'billing' => ['ROLE_ADMIN'],
        'inventory' => ['ROLE_ADMIN'],
        'settings' => ['ROLE_ADMIN'],
        'users' => ['ROLE_ADMIN'],
        // Mantenedores - solo ADMIN
        'maintenance_countries' => ['ROLE_ADMIN'],
        'maintenance_regions' => ['ROLE_ADMIN'],
        'maintenance_provinces' => ['ROLE_ADMIN'],
        'maintenance_municipalities' => ['ROLE_ADMIN'],
        'maintenance_genders' => ['ROLE_ADMIN'],
        'maintenance_marital_status' => ['ROLE_ADMIN'],
        'maintenance_ethnic_groups' => ['ROLE_ADMIN'],
    ];

    public function canAccess(string $moduleName, array $userRoles): bool
    {
        // Si no está configurado el módulo, solo ADMIN
        if (!isset(self::DEFAULT_MODULE_PERMISSIONS[$moduleName])) {
            return in_array('ROLE_ADMIN', $userRoles);
        }

        $allowedRoles = self::DEFAULT_MODULE_PERMISSIONS[$moduleName];
        
        return !empty(array_intersect($userRoles, $allowedRoles));
    }

    public function getType(): string
    {
        return 'restrictive';
    }
}
