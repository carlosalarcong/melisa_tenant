<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\InsuranceAdministrator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InsuranceAdministrator>
 */
class InsuranceAdministratorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsuranceAdministrator::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.active = :active')
            ->setParameter('active', true)
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
