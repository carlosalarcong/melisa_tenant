<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Estados de entidades en el sistema
 * 
 * ACTIVE: Entidad activa y disponible
 * INACTIVE: Entidad inactiva (eliminación lógica)
 * BLOCKED: Entidad bloqueada temporalmente
 */
enum StateEnum: int
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
            self::INACTIVE => 'danger',
            self::BLOCKED => 'warning'
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public static function fromString(string $name): self
    {
        return match(strtoupper($name)) {
            'ACTIVO', 'ACTIVE' => self::ACTIVE,
            'INACTIVO', 'INACTIVE' => self::INACTIVE,
            'BLOQUEADO', 'BLOCKED' => self::BLOCKED,
            default => throw new \InvalidArgumentException("Estado no válido: {$name}")
        };
    }
}
