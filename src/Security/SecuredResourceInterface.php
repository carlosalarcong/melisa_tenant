<?php

namespace App\Security;

/**
 * Interface para recursos que requieren control de permisos granular.
 * 
 * Cualquier entidad que implemente esta interface podrá ser protegida
 * a nivel de instancia y campo mediante el sistema de permisos.
 */
interface SecuredResourceInterface
{
    /**
     * Retorna el dominio/tipo de recurso para permisos.
     * 
     * Ejemplos: 'persona', 'patient', 'appointment', 'invoice'
     * 
     * @return string El nombre del dominio del recurso
     */
    public function getPermissionDomain(): string;

    /**
     * Retorna el identificador único de esta instancia del recurso.
     * 
     * @return int|string El ID del recurso
     */
    public function getPermissionId(): int|string;
}
