<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Géneros biológicos para registro de usuarios
 */
enum GenderEnum: int
{
    case MALE = 1;
    case FEMALE = 2;
    case OTHER = 3;

    public function label(): string
    {
        return match($this) {
            self::MALE => 'Masculino',
            self::FEMALE => 'Femenino',
            self::OTHER => 'Otro'
        };
    }

    public function abbreviation(): string
    {
        return match($this) {
            self::MALE => 'M',
            self::FEMALE => 'F',
            self::OTHER => 'O'
        };
    }

    public static function fromAbbreviation(string $abbr): self
    {
        return match(strtoupper($abbr)) {
            'M', 'MALE', 'MASCULINO' => self::MALE,
            'F', 'FEMALE', 'FEMENINO' => self::FEMALE,
            'O', 'OTHER', 'OTRO' => self::OTHER,
            default => throw new \InvalidArgumentException("Género no válido: {$abbr}")
        };
    }
}
