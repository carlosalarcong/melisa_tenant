<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\ServiceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceType>
 */
class ServiceTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceType::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('st')
            ->where('st.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('st.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCode(string $code): ?ServiceType
    {
        return $this->createQueryBuilder('st')
            ->where('st.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
