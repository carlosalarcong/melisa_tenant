<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\MemberGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MemberGroup>
 */
class MemberGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberGroup::class);
    }

    /**
     * Encuentra grupos activos por código.
     */
    public function findActiveByCode(string $code): ?MemberGroup
    {
        return $this->findOneBy([
            'code' => $code,
            'active' => true,
        ]);
    }

    /**
     * Encuentra todos los grupos activos.
     *
     * @return MemberGroup[]
     */
    public function findAllActive(): array
    {
        return $this->findBy(['active' => true], ['name' => 'ASC']);
    }
}
