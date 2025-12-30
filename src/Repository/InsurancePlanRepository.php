<?php

namespace App\Repository;

use App\Entity\Tenant\InsurancePlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InsurancePlan>
 */
class InsurancePlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsurancePlan::class);
    }

    /**
     * Mock: Retorna planes de seguro activos
     */
    public function findAllActive(): array
    {
        $mock1 = new InsurancePlan();
        $mock1->setName('FONASA');
        $mock1->setAbbreviatedName('FONASA');
        $mock1->setPlanCode(1);
        $mock1->setStateId(1);
        $mock1->setOrganizationId(2);
        $mock1->setIsActive(true);

        $mock2 = new InsurancePlan();
        $mock2->setName('ISAPRE');
        $mock2->setAbbreviatedName('ISAPRE');
        $mock2->setPlanCode(2);
        $mock2->setStateId(1);
        $mock2->setOrganizationId(2);
        $mock2->setIsActive(true);

        $mock3 = new InsurancePlan();
        $mock3->setName('Particular');
        $mock3->setAbbreviatedName('Particular');
        $mock3->setPlanCode(3);
        $mock3->setStateId(1);
        $mock3->setOrganizationId(2);
        $mock3->setIsActive(true);

        return [$mock1, $mock2, $mock3];
    }
}
