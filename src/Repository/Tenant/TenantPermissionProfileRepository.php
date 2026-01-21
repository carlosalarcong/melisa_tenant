<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\TenantPermissionProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TenantPermissionProfile>
 */
class TenantPermissionProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TenantPermissionProfile::class);
    }

    /**
     * Obtiene el perfil de permisos activo del tenant.
     * Debería haber solo uno, retorna el primero.
     */
    public function getCurrentProfile(): ?TenantPermissionProfile
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
