<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\DoctorType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DoctorType>
 */
class DoctorTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DoctorType::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.active = :active')
            ->setParameter('active', true)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
