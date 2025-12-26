<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Estados de servicios médicos de un usuario
 * 
 * IMPORTANTE: Solo UN servicio puede estar ACTIVE a la vez por usuario
 */
enum ServiceStateEnum: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
    case BLOCKED = 2;

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Activo',
            self::INACTIVE => 'Inactivo',
            self::BLOCKED => 'Bloqueado'
        };
    }

    public function canBeActivated(): bool
    {
        return $this === self::INACTIVE;
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}
