<?php

namespace App\Repository;

use App\Entity\Tenant\BranchHealthInsurance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BranchHealthInsurance>
 */
class BranchHealthInsuranceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BranchHealthInsurance::class);
    }

    // Mock methods
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
