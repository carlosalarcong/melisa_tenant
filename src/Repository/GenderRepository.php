<?php

namespace App\Repository;

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
     * Searches genders by search term
     */
    public function findBySearchTerm(string $searchTerm): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.name LIKE :term OR g.code LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds active genders
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Checks if a specific code exists
     */
    public function existsByCode(string $code, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('g')
            ->select('COUNT(g.id)')
            ->andWhere('g.code = :code')
            ->setParameter('code', $code);

        if ($excludeId) {
            $qb->andWhere('g.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}