<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\JobPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobPosition>
 */
class JobPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPosition::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('j')
            ->where('j.active = :active')
            ->setParameter('active', true)
            ->orderBy('j.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
