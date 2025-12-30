<?php

namespace App\Repository;

use App\Entity\Tenant\CreditCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreditCard>
 */
class CreditCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreditCard::class);
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
    public function find($id, $lockMode = null, $lockVersion = null): ?CreditCard
    {
        return null;
    }
}
