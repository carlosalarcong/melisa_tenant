<?php

namespace App\Repository\Tenant;

use App\Entity\Tenant\EducationLevelDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EducationLevelDetail>
 */
class EducationLevelDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EducationLevelDetail::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.active = :active')
            ->setParameter('active', true)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
