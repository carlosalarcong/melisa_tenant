<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Estados específicos de perfiles de usuario
 * 
 * IMPORTANTE: Estado INACTIVE en perfil = EXCLUSIÓN EXPLÍCITA
 * No es lo mismo que "no tener el perfil asignado"
 */
enum ProfileStateEnum: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Activo',
            self::INACTIVE => 'Excluido'
        };
    }

    public function description(): string
    {
        return match($this) {
            self::ACTIVE => 'Perfil activo para el usuario',
            self::INACTIVE => 'Perfil explícitamente excluido (no heredado de grupos)'
        };
    }

    /**
     * CRÍTICO: Estado INACTIVE = EXCLUSIÓN EXPLÍCITA
     * Anula la herencia de permisos desde grupos
     */
    public function isExplicitExclusion(): bool
    {
        return $this === self::INACTIVE;
    }
}
