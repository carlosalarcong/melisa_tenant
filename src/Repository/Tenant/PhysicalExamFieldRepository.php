<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\PhysicalExamField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PhysicalExamField>
 */
class PhysicalExamFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhysicalExamField::class);
    }

    /**
     * Encuentra todos los campos de examen físico activos
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('pef')
            ->where('pef.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('pef.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra campos de examen físico por agrupación
     */
    public function findByGrouping(int $groupingId): array
    {
        return $this->createQueryBuilder('pef')
            ->where('pef.grouping = :groupingId')
            ->setParameter('groupingId', $groupingId)
            ->orderBy('pef.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
