<?php

namespace App\Repository;

use App\Entity\Tenant\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Organization>
 */
class OrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organization::class);
    }

    /**
     * Encuentra la organización principal (ID: 2)
     * Para multi-tenant, retorna la organización del tenant actual
     */
    public function findMainOrganization(): ?Organization
    {
        return $this->find(2); // ID 2 es Melisa La Colina
    }
}
