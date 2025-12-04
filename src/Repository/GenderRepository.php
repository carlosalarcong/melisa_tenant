<?php

namespace App\Repository;

use App\Entity\Gender;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository para Gender usando TenantEntityManager
 */
class GenderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gender::class);
    }

    /**
     * Busca géneros activos
     * 
     * @return Gender[]
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca géneros para personas
     * 
     * @return Gender[]
     */
    public function findForPersons(): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.isPerson = :isPerson')
            ->andWhere('g.isActive = :active')
            ->setParameter('isPerson', true)
            ->setParameter('active', true)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca por código HL7
     */
    public function findByHl7Code(string $code): ?Gender
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.genderCodeHl7 = :code')
            ->andWhere('g.isActive = :active')
            ->setParameter('code', $code)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
