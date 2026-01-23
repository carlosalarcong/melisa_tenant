<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.active = :active')
            ->setParameter('active', true)
            ->orderBy('l.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
