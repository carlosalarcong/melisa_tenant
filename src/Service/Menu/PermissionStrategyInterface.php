<?php

namespace App\Service\Menu;

/**
 * Interface para estrategias de permisos de módulos.
 */
interface PermissionStrategyInterface
{
    /**
     * Determina si un usuario tiene acceso a un módulo específico.
     *
     * @param string $moduleName Nombre del módulo
     * @param array $userRoles Roles del usuario
     * @return bool
     */
    public function canAccess(string $moduleName, array $userRoles): bool;

    /**
     * Retorna el tipo de estrategia.
     *
     * @return string 'collaborative', 'restrictive', 'custom'
     */
    public function getType(): string;
}
