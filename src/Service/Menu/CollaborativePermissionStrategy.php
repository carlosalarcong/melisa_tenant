<?php

namespace App\Service\Menu;

/**
 * Estrategia Collaborative: Múltiples roles pueden acceder.
 * Usada en clínicas grandes con diferentes tipos de profesionales.
 */
class CollaborativePermissionStrategy implements PermissionStrategyInterface
{
    /**
     * Configuración por defecto de módulos para perfil collaborative.
     * Define qué roles pueden acceder a cada módulo.
     */
    private const DEFAULT_MODULE_PERMISSIONS = [
        'dashboard' => ['ROLE_ADMIN', 'ROLE_DOCTOR', 'ROLE_SECRETARY', 'ROLE_USER'],
        'patients' => ['ROLE_ADMIN', 'ROLE_DOCTOR', 'ROLE_SECRETARY'],
        'appointments' => ['ROLE_ADMIN', 'ROLE_DOCTOR', 'ROLE_SECRETARY'],
        'medical_records' => ['ROLE_ADMIN', 'ROLE_DOCTOR'],
        'reports' => ['ROLE_ADMIN', 'ROLE_DOCTOR'],
        'billing' => ['ROLE_ADMIN', 'ROLE_SECRETARY'],
        'inventory' => ['ROLE_ADMIN', 'ROLE_SECRETARY'],
        'settings' => ['ROLE_ADMIN'],
        'users' => ['ROLE_ADMIN'],
        // Mantenedores
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
        // Si no está configurado el módulo, permitir acceso a ADMIN por defecto
        if (!isset(self::DEFAULT_MODULE_PERMISSIONS[$moduleName])) {
            return in_array('ROLE_ADMIN', $userRoles);
        }

        $allowedRoles = self::DEFAULT_MODULE_PERMISSIONS[$moduleName];
        
        // Verificar si el usuario tiene alguno de los roles permitidos
        return !empty(array_intersect($userRoles, $allowedRoles));
    }

    public function getType(): string
    {
        return 'collaborative';
    }
}
