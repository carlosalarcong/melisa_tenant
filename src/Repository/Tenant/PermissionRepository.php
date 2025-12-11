<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\Permission;
use App\Entity\Tenant\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Permission>
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    /**
     * Encuentra todos los permisos de un usuario.
     *
     * @return Permission[]
     */
    public function findAllByMember(Member $member): array
    {
        return $this->findBy(['user' => $member]);
    }

    /**
     * Encuentra permisos específicos para un dominio y recurso.
     */
    public function findByDomainAndResource(
        Member $member,
        string $domain,
        ?int $resourceId = null,
        ?string $fieldName = null
    ): ?Permission {
        return $this->findOneBy([
            'user' => $member,
            'domain' => $domain,
            'resourceId' => $resourceId,
            'fieldName' => $fieldName,
        ]);
    }
}
