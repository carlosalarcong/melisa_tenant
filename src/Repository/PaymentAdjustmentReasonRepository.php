<?php

namespace App\Repository;

use App\Entity\Tenant\PaymentAdjustmentReason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentAdjustmentReason>
 */
class PaymentAdjustmentReasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentAdjustmentReason::class);
    }

    /**
     * Mock: Retorna motivos de ajuste activos
     */
    public function findAllActive(): array
    {
        $mock1 = new PaymentAdjustmentReason();
        $mock1->setName('Error de digitación');
        $mock1->setStateId(1);
        $mock1->setOrganizationId(2);
        $mock1->setIsActive(true);

        $mock2 = new PaymentAdjustmentReason();
        $mock2->setName('Cortesía');
        $mock2->setStateId(1);
        $mock2->setOrganizationId(2);
        $mock2->setIsActive(true);

        return [$mock1, $mock2];
    }
}
