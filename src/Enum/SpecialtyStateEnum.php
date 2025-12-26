<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Estados de especialidades médicas de un usuario
 * 
 * IMPORTANTE: Especialidades con fecha NO se pueden desasignar, solo bloquear
 */
enum SpecialtyStateEnum: int
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

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'secondary',
            self::BLOCKED => 'danger'
        };
    }

    /**
     * Especialidades con fecha solo pueden bloquearse, no eliminarse
     */
    public function allowsRemoval(): bool
    {
        return $this === self::INACTIVE;
    }
}
