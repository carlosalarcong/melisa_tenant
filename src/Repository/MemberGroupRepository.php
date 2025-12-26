<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\MemberGroup;
use App\Entity\Tenant\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for MemberGroup entity
 */
class MemberGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberGroup::class);
    }

    /**
     * Remove all groups for a member
     * 
     * @param Member $member
     */
    public function removeAllByMember(Member $member): void
    {
        $this->createQueryBuilder('mg')
            ->delete()
            ->where('mg.member = :member')
            ->setParameter('member', $member)
            ->getQuery()
            ->execute();
    }

    /**
     * Get all member groups
     * 
     * @param Member $member
     * @return MemberGroup[]
     */
    public function findByMember(Member $member): array
    {
        return $this->findBy(['member' => $member]);
    }
}
