<?php

namespace App\Repository;

use App\Entity\Tenant\FreeChargeReason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FreeChargeReason>
 */
class FreeChargeReasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FreeChargeReason::class);
    }

    /**
     * Mock: Retorna lista vacía
     * TODO: Implementar consultas reales cuando se necesiten
     */
    public function findAll(): array
    {
        return [];
    }

    /**
     * Mock: Retorna null
     * TODO: Implementar consulta real cuando se necesite
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?FreeChargeReason
    {
        return null;
    }
}
