<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\Department;
use App\Entity\Tenant\Branch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Department entity
 */
class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    /**
     * Find active departments by branch
     * 
     * @param Branch $branch
     * @return Department[]
     */
    public function findActiveByBranch(Branch $branch): array
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.state', 's')
            ->where('d.branch = :branch')
            ->andWhere('s.description = :active')
            ->setParameter('branch', $branch)
            ->setParameter('active', 'ACTIVE')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find active departments by branch ID
     * For AJAX cascade loading
     * 
     * @param int $branchId
     * @return Department[]
     */
    public function findActiveByBranchId(int $branchId): array
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.state', 's')
            ->where('d.branch = :branchId')
            ->andWhere('s.description = :active')
            ->setParameter('branchId', $branchId)
            ->setParameter('active', 'ACTIVE')
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
