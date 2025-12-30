<?php

namespace App\Repository;

use App\Entity\Tenant\ReferralSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReferralSource>
 */
class ReferralSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReferralSource::class);
    }

    // Mock methods - retornan arrays vacíos
    public function findAll(): array
    {
        return [];
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return [];
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        return null;
    }
}
