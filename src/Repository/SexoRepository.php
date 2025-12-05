<?php

namespace App\Repository;

use App\Entity\Sexo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sexo>
 */
class SexoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sexo::class);
    }

    /**
     * Busca sexos por término de búsqueda
     */
    public function findBySearchTerm(string $searchTerm): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nombre LIKE :term OR s.descripcion LIKE :term OR s.codigo LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->orderBy('s.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra sexos activos
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.activo = :activo')
            ->setParameter('activo', true)
            ->orderBy('s.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Verifica si existe un código específico
     */
    public function existsByCodigo(string $codigo, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.codigo = :codigo')
            ->setParameter('codigo', $codigo);

        if ($excludeId) {
            $qb->andWhere('s.id != :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}