<?php

namespace App\Repository;

use App\Entity\Tenant\PaymentAdjustmentDirection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentAdjustmentDirection>
 */
class PaymentAdjustmentDirectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentAdjustmentDirection::class);
    }

    /**
     * Mock: Retorna direcciones de ajuste activas
     */
    public function findAllActive(): array
    {
        $mock1 = new PaymentAdjustmentDirection();
        $mock1->setName('Incremento');
        $mock1->setIsActive(true);

        $mock2 = new PaymentAdjustmentDirection();
        $mock2->setName('Descuento');
        $mock2->setIsActive(true);

        return [$mock1, $mock2];
    }
}
