<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant\PasswordHistory;
use App\Entity\Tenant\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for PasswordHistory entity
 */
class PasswordHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordHistory::class);
    }

    /**
     * Get last N password hashes for a member
     * 
     * @param Member $member
     * @param int $limit
     * @return PasswordHistory[]
     */
    public function getLastPasswords(Member $member, int $limit): array
    {
        return $this->createQueryBuilder('ph')
            ->where('ph.member = :member')
            ->setParameter('member', $member)
            ->orderBy('ph.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all password history for a member ordered by date
     * 
     * @param Member $member
     * @return PasswordHistory[]
     */
    public function getAllByMember(Member $member): array
    {
        return $this->createQueryBuilder('ph')
            ->where('ph.member = :member')
            ->setParameter('member', $member)
            ->orderBy('ph.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Delete old password records keeping only the N most recent
     * 
     * @param Member $member
     * @param int $keepCount Number of records to keep
     */
    public function deleteOldPasswords(Member $member, int $keepCount): void
    {
        // Get IDs to keep
        $idsToKeep = $this->createQueryBuilder('ph')
            ->select('ph.id')
            ->where('ph.member = :member')
            ->setParameter('member', $member)
            ->orderBy('ph.createdAt', 'DESC')
            ->setMaxResults($keepCount)
            ->getQuery()
            ->getSingleColumnResult();

        if (empty($idsToKeep)) {
            return;
        }

        // Delete records not in the keep list
        $this->createQueryBuilder('ph')
            ->delete()
            ->where('ph.member = :member')
            ->andWhere('ph.id NOT IN (:idsToKeep)')
            ->setParameter('member', $member)
            ->setParameter('idsToKeep', $idsToKeep)
            ->getQuery()
            ->execute();
    }
}
