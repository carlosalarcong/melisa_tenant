<?php

namespace App\Repository;

use App\Entity\Tenant\CashierStation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository para CashierStation usando TenantEntityManager
 */
class CashierStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CashierStation::class);
    }

    /**
     * Busca la estación de cajero activa para un usuario
     */
    public function findActiveStationByMember(int $memberId): ?CashierStation
    {
        return $this->createQueryBuilder('cs')
            ->andWhere('cs.member = :memberId')
            ->andWhere('cs.isActive = :active')
            ->setParameter('memberId', $memberId)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Mock temporal para compatibilidad con código Legacy
     * TODO: Implementar lógica real cuando se migren datos
     */
    public function findOneByMemberAndState(int $memberId, bool $isActive = true): ?object
    {
        // Mock temporal: retorna un objeto simulado
        // En producción, esto consultará la tabla cashier_station
        return (object)[
            'id' => 1,
            'initialAmount' => 0,
            'isActive' => true
        ];
    }
}
