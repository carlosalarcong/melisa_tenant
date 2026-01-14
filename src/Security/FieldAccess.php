<?php

namespace App\Security;

/**
 * Value Object que representa el acceso a un campo específico de un recurso.
 * 
 * Se usa para verificar permisos granulares a nivel de campo:
 * - ¿Puede ver el campo 'salario' de esta persona?
 * - ¿Puede editar el campo 'temperatura' de este paciente?
 */
final readonly class FieldAccess
{
    public function __construct(
        public SecuredResourceInterface $resource,
        public string $field,
    ) {
    }

    /**
     * Retorna una representación string para debugging.
     */
    public function __toString(): string
    {
        return sprintf(
            '%s:%s.%s',
            $this->resource->getPermissionDomain(),
            $this->resource->getPermissionId(),
            $this->field
        );
    }
}
