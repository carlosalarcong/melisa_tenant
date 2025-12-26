<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\License;
use App\Entity\Tenant\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for License entity
 */
class LicenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, License::class);
    }

    /**
     * Get license usage information for an organization
     * Uses FOR UPDATE lock to prevent race conditions
     * 
     * @param Organization $organization
     * @return array ['total' => int, 'used' => int, 'available' => int]
     */
    public function getLicenseUsage(Organization $organization): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        // Atomic query with FOR UPDATE lock
        $sql = "
            SELECT 
                l.quantity as total,
                COUNT(DISTINCT m.id) as used
            FROM license l
            LEFT JOIN person p ON p.organization_id = l.organization_id
            LEFT JOIN member m ON m.person_id = p.id
            LEFT JOIN state s ON m.state_id = s.id AND s.name = 'ACTIVE'
            WHERE l.organization_id = :organizationId
            GROUP BY l.quantity
            FOR UPDATE
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['organizationId' => $organization->getId()]);
        $data = $result->fetchAssociative();
        
        if (!$data) {
            return [
                'total' => 0,
                'used' => 0,
                'available' => 0
            ];
        }
        
        $total = (int) ($data['total'] ?? 0);
        $used = (int) ($data['used'] ?? 0);
        $available = max(0, $total - $used);
        
        return [
            'total' => $total,
            'used' => $used,
            'available' => $available
        ];
    }

    /**
     * Get license by organization
     * 
     * @param Organization $organization
     * @return License|null
     */
    public function findByOrganization(Organization $organization): ?License
    {
        return $this->findOneBy(['organization' => $organization]);
    }
}
