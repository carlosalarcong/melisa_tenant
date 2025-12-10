<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\GroupPermission;
use App\Entity\Tenant\MemberGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupPermission>
 */
class GroupPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupPermission::class);
    }

    /**
     * Encuentra todos los permisos de un grupo.
     *
     * @return GroupPermission[]
     */
    public function findAllByGroup(MemberGroup $group): array
    {
        return $this->findBy(['group' => $group]);
    }

    /**
     * Encuentra permisos para múltiples grupos.
     *
     * @param MemberGroup[] $groups
     * @return GroupPermission[]
     */
    public function findByGroups(array $groups): array
    {
        if (empty($groups)) {
            return [];
        }

        return $this->createQueryBuilder('gp')
            ->where('gp.group IN (:groups)')
            ->setParameter('groups', $groups)
            ->getQuery()
            ->getResult();
    }
}
