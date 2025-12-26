<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\Profile;
use App\Entity\Tenant\Member;
use App\Entity\Tenant\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Profile entity
 */
class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    /**
     * Get active profiles for a member
     * Considers group inheritance and explicit exclusions
     * 
     * REGLA CRÍTICA: Estado INACTIVE en perfil = EXCLUSIÓN EXPLÍCITA
     * 
     * @param Member $member
     * @return array Array of profile IDs
     */
    public function getActiveMemberProfiles(Member $member): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        // Query that considers explicit exclusions
        $sql = "
            SELECT DISTINCT p.id
            FROM profile p
            WHERE p.id IN (
                -- Profiles from groups
                SELECT gp.profile_id
                FROM user_group g
                INNER JOIN member_group mg ON g.id = mg.group_id
                INNER JOIN group_profile gp ON g.id = gp.group_id
                WHERE mg.member_id = :memberId
                AND g.state_id = (SELECT id FROM state WHERE name = 'ACTIVE' LIMIT 1)
                AND gp.is_active = 1
                
                UNION
                
                -- Direct active profiles
                SELECT mp.profile_id
                FROM member_profile mp
                WHERE mp.member_id = :memberId
                AND mp.is_active = 1
            )
            -- Exclude profiles with explicit exclusion
            AND p.id NOT IN (
                SELECT mp2.profile_id
                FROM member_profile mp2
                WHERE mp2.member_id = :memberId
                AND mp2.is_active = 0  -- Estado INACTIVE = Explicit exclusion
            )
            AND p.state_id = (SELECT id FROM state WHERE name = 'ACTIVE' LIMIT 1)
        ";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['memberId' => $member->getId()]);
        
        return array_column($result->fetchAllAssociative(), 'id');
    }
}
