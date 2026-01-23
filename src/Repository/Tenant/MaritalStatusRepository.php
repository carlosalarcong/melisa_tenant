<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\MaritalStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MaritalStatus>
 */
class MaritalStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaritalStatus::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.active = :active')
            ->setParameter('active', true)
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
