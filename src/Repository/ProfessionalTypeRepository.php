<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\ProfessionalType;
use App\Entity\Tenant\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for ProfessionalType entity
 */
class ProfessionalTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfessionalType::class);
    }

    /**
     * Find active professional types by organization
     * 
     * @param Organization $organization
     * @return ProfessionalType[]
     */
    public function findActiveByOrganization(Organization $organization): array
    {
        return $this->createQueryBuilder('pt')
            ->innerJoin('pt.state', 's')
            ->where('pt.organization = :organization')
            ->andWhere('s.description = :active')
            ->setParameter('organization', $organization)
            ->setParameter('active', 'ACTIVE')
            ->orderBy('pt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find professional type by code
     * 
     * @param string $code
     * @param Organization $organization
     * @return ProfessionalType|null
     */
    public function findByCode(string $code, Organization $organization): ?ProfessionalType
    {
        return $this->createQueryBuilder('pt')
            ->where('pt.code = :code')
            ->andWhere('pt.organization = :organization')
            ->setParameter('code', $code)
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
