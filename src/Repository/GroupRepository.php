<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\Group;
use App\Entity\Tenant\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Group entity
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * Get active groups for a member
     * 
     * @param Member $member
     * @return array Array of Group entities
     */
    public function getActiveMemberGroups(Member $member): array
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('App\Entity\Tenant\MemberGroup', 'mg', 'WITH', 'mg.groupId = g.id')
            ->innerJoin('g.state', 's')
            ->where('mg.memberId = :memberId')
            ->andWhere('s.name = :activeState')
            ->setParameter('memberId', $member->getId())
            ->setParameter('activeState', 'ACTIVE')
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
