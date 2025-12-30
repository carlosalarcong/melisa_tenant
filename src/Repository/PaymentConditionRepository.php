<?php

namespace App\Repository;

use App\Entity\Tenant\PaymentCondition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentCondition>
 *
 * @method PaymentCondition|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentCondition|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentCondition[]    findAll()
 * @method PaymentCondition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentConditionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentCondition::class);
    }

    /**
     * Mock: Retorna condiciones de pago activas
     */
    public function findAllActive(): array
    {
        $mock1 = new PaymentCondition();
        $mock1->setName('Contado');
        $mock1->setInterfaceCode('CONTADO');
        $mock1->setMaxTerm(0);
        $mock1->setIsOnDay(true);
        $mock1->setStateId(1);
        $mock1->setOrganizationId(2);

        $mock2 = new PaymentCondition();
        $mock2->setName('30 días');
        $mock2->setInterfaceCode('30DIAS');
        $mock2->setMaxTerm(30);
        $mock2->setIsOnDay(false);
        $mock2->setStateId(1);
        $mock2->setOrganizationId(2);

        return [$mock1, $mock2];
    }
}
