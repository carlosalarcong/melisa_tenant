<?php

namespace App\Repository;

use App\Entity\Tenant\ReopeningState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReopeningState>
 */
class ReopeningStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReopeningState::class);
    }

    // Mock methods
    public function find($id, $lockMode = null, $lockVersion = null): ?ReopeningState
    {
        $state = new ReopeningState();
        $state->setId((int)$id);
        $state->setName($id == 1 ? 'Abierta' : 'Cerrada');
        return $state;
    }

    public function findAll(): array
    {
        return [];
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return [];
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        return null;
    }
}
