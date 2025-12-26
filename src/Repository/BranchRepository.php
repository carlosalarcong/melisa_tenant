<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\Branch;
use App\Entity\Tenant\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Branch entity
 */
class BranchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Branch::class);
    }

    /**
     * Find active branches by organization
     * 
     * @param Organization $organization
     * @return Branch[]
     */
    public function findActiveByOrganization(Organization $organization): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.state', 's')
            ->where('b.organization = :organization')
            ->andWhere('s.description = :active')
            ->setParameter('organization', $organization)
            ->setParameter('active', 'ACTIVE')
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find branch by code
     * 
     * @param string $code
     * @param Organization $organization
     * @return Branch|null
     */
    public function findByCode(string $code, Organization $organization): ?Branch
    {
        return $this->createQueryBuilder('b')
            ->where('b.code = :code')
            ->andWhere('b.organization = :organization')
            ->setParameter('code', $code)
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
