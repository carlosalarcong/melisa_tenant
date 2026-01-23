<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\Religion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Religion>
 */
class ReligionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Religion::class);
    }

    /**
     * Encuentra todas las religiones activas
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.active = :active')
            ->setParameter('active', true)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca religión por código
     */
    public function findByCode(string $code): ?Religion
    {
        return $this->createQueryBuilder('r')
            ->where('r.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
