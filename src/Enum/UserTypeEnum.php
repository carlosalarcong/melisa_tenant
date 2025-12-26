<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Tipo de usuario en el sistema
 * 
 * PROFESSIONAL: Médico, enfermera, profesional de la salud (requiere especialidades)
 * ADMINISTRATIVE: Usuario administrativo sin especialidades médicas
 */
enum UserTypeEnum: int
{
    case PROFESSIONAL = 0;
    case ADMINISTRATIVE = 1;

    public function label(): string
    {
        return match($this) {
            self::PROFESSIONAL => 'Profesional',
            self::ADMINISTRATIVE => 'Usuario Administrativo'
        };
    }

    public function description(): string
    {
        return match($this) {
            self::PROFESSIONAL => 'Profesional de la salud con especialidades médicas',
            self::ADMINISTRATIVE => 'Usuario administrativo del sistema'
        };
    }

    public function requiresSpecialties(): bool
    {
        return $this === self::PROFESSIONAL;
    }
}
