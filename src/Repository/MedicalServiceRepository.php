<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\MedicalService;
use App\Entity\Tenant\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for MedicalService entity
 */
class MedicalServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalService::class);
    }

    /**
     * Find active services by department
     * 
     * @param Department $department
     * @return MedicalService[]
     */
    public function findActiveByDepartment(Department $department): array
    {
        return $this->createQueryBuilder('ms')
            ->innerJoin('ms.state', 's')
            ->where('ms.department = :department')
            ->andWhere('s.description = :active')
            ->setParameter('department', $department)
            ->setParameter('active', 'ACTIVE')
            ->orderBy('ms.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find active services by department ID
     * For AJAX cascade loading
     * 
     * @param int $departmentId
     * @return MedicalService[]
     */
    public function findActiveByDepartmentId(int $departmentId): array
    {
        return $this->createQueryBuilder('ms')
            ->innerJoin('ms.state', 's')
            ->where('ms.department = :departmentId')
            ->andWhere('s.description = :active')
            ->setParameter('departmentId', $departmentId)
            ->setParameter('active', 'ACTIVE')
            ->orderBy('ms.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find service by HL7 service type
     * 
     * @param string $hl7Type
     * @return MedicalService[]
     */
    public function findByHl7Type(string $hl7Type): array
    {
        return $this->createQueryBuilder('ms')
            ->where('ms.hl7ServiceType = :type')
            ->setParameter('type', $hl7Type)
            ->getQuery()
            ->getResult();
    }
}
