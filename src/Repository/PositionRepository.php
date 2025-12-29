<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\Position;
use App\Entity\Tenant\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Position entity
 */
class PositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    /**
     * Find active positions by organization
     * 
     * @param Organization $organization
     * @return Position[]
     */
    public function findActiveByOrganization(Organization $organization): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.state', 's')
            ->where('p.organization = :organization')
            ->andWhere('s.description = :active')
            ->setParameter('organization', $organization)
            ->setParameter('active', 'ACTIVE')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find position by code
     * 
     * @param string $code
     * @param Organization $organization
     * @return Position|null
     */
    public function findByCode(string $code, Organization $organization): ?Position
    {
        return $this->createQueryBuilder('p')
            ->where('p.code = :code')
            ->andWhere('p.organization = :organization')
            ->setParameter('code', $code)
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
