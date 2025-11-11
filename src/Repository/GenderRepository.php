<?php

namespace App\Repository;

use App\Entity\Gender;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Repository para Gender usando TenantEntityManager
 */
class GenderRepository extends EntityRepository
{
    private TenantEntityManager $entityManager;

    public function __construct(TenantEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($entityManager, $entityManager->getClassMetadata(Gender::class));
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
