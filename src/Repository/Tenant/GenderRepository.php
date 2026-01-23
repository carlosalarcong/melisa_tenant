<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\Gender;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gender>
 */
class GenderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gender::class);
    }

    /**
     * Encuentra todos los géneros activos
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.active = :active')
            ->setParameter('active', true)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca género por código
     */
    public function findByCode(string $code): ?Gender
    {
        return $this->createQueryBuilder('g')
            ->where('g.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
