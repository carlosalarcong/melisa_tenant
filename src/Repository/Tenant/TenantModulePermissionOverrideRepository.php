<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\TenantModulePermissionOverride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TenantModulePermissionOverride>
 */
class TenantModulePermissionOverrideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TenantModulePermissionOverride::class);
    }

    /**
     * Obtiene el override de permisos para un módulo específico.
     */
    public function findByModuleName(string $moduleName): ?TenantModulePermissionOverride
    {
        return $this->findOneBy([
            'moduleName' => $moduleName,
            'isActive' => true
        ]);
    }

    /**
     * Obtiene todos los overrides activos.
     *
     * @return TenantModulePermissionOverride[]
     */
    public function findAllActive(): array
    {
        return $this->findBy(['isActive' => true], ['moduleName' => 'ASC']);
    }

    /**
     * Obtiene los roles requeridos para un módulo.
     * Retorna array vacío si no hay override definido.
     */
    public function getRequiredRolesForModule(string $moduleName): array
    {
        $override = $this->findByModuleName($moduleName);
        
        return $override ? $override->getRequiredRoles() : [];
    }
}
