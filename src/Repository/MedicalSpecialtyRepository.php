<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\MedicalSpecialty;
use App\Entity\Tenant\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for MedicalSpecialty entity
 */
class MedicalSpecialtyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalSpecialty::class);
    }

    /**
     * Find active specialties by organization
     * 
     * @param Organization $organization
     * @return MedicalSpecialty[]
     */
    public function findActiveByOrganization(Organization $organization): array
    {
        return $this->createQueryBuilder('ms')
            ->innerJoin('ms.state', 's')
            ->where('ms.organization = :organization')
            ->andWhere('s.description = :active')
            ->setParameter('organization', $organization)
            ->setParameter('active', 'ACTIVE')
            ->orderBy('ms.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find specialty by SNOMED code
     * 
     * @param string $snomedCode
     * @param Organization $organization
     * @return MedicalSpecialty|null
     */
    public function findBySnomedCode(string $snomedCode, Organization $organization): ?MedicalSpecialty
    {
        return $this->createQueryBuilder('ms')
            ->where('ms.snomedCode = :code')
            ->andWhere('ms.organization = :organization')
            ->setParameter('code', $snomedCode)
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find specialty by HL7 code
     * 
     * @param string $hl7Code
     * @param Organization $organization
     * @return MedicalSpecialty|null
     */
    public function findByHl7Code(string $hl7Code, Organization $organization): ?MedicalSpecialty
    {
        return $this->createQueryBuilder('ms')
            ->where('ms.hl7SpecialtyCode = :code')
            ->andWhere('ms.organization = :organization')
            ->setParameter('code', $hl7Code)
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
