<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Region>
 */
class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    /**
     * Busca regiones por término de búsqueda
     */
    public function findBySearchTerm(string $searchTerm): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.nombre LIKE :term OR r.descripcion LIKE :term OR r.codigo LIKE :term OR r.pais LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->orderBy('r.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra regiones activas
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.activo = :activo')
            ->setParameter('activo', true)
            ->orderBy('r.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra regiones por país
     */
    public function findByPais(string $pais): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.pais = :pais')
            ->andWhere('r.activo = :activo')
            ->setParameter('pais', $pais)
            ->setParameter('activo', true)
            ->orderBy('r.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Verifica si existe un código específico
     */
    public function existsByCodigo(string $codigo, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.codigo = :codigo')
            ->setParameter('codigo', $codigo);

        if ($excludeId) {
            $qb->andWhere('r.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}