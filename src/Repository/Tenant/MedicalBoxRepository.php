<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\MedicalBox;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalBox>
 */
class MedicalBoxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalBox::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.active = :active')
            ->setParameter('active', true)
            ->orderBy('m.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
