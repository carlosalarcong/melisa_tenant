<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\Occupation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Occupation>
 */
class OccupationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Occupation::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.active = :active')
            ->setParameter('active', true)
            ->orderBy('o.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
