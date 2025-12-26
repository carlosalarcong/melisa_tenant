<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\MemberProfile;
use App\Entity\Tenant\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for MemberProfile entity
 */
class MemberProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberProfile::class);
    }

    /**
     * Remove all profiles for a member
     * 
     * @param Member $member
     */
    public function removeAllByMember(Member $member): void
    {
        $this->createQueryBuilder('mp')
            ->delete()
            ->where('mp.member = :member')
            ->setParameter('member', $member)
            ->getQuery()
            ->execute();
    }

    /**
     * Get all member profiles
     * 
     * @param Member $member
     * @return MemberProfile[]
     */
    public function findByMember(Member $member): array
    {
        return $this->findBy(['member' => $member]);
    }
}
