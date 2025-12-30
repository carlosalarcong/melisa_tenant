<?php

namespace App\Repository;

use App\Entity\Tenant\SystemParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository para SystemParameter usando TenantEntityManager
 */
class SystemParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemParameter::class);
    }

    /**
     * Obtiene un parámetro por su nombre
     */
    public function findByName(string $name): ?SystemParameter
    {
        return $this->createQueryBuilder('sp')
            ->andWhere('sp.name = :name')
            ->andWhere('sp.isActive = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Mock temporal para compatibilidad con código Legacy
     * Retorna un array con estructura compatible
     * TODO: Implementar lógica real cuando se migren parámetros
     */
    public function obtenerParametro(string $parameterName): array
    {
        // Mock temporal: retorna valores por defecto según el parámetro solicitado
        $mockParameters = [
            'HABILITAR_PAIS_NACIONALIDAD_EXTRANJERO' => ['valor' => '1'],
            'FOLIO_GLOBAL' => ['valor' => '0'],
            'APLICAR_DIFERENCIA_INDIVIDUAL' => ['valor' => '1'],
            'APLICAR_DIFERENCIA_SALDO' => ['valor' => '1'],
            'HABILITAR_RESTRICCIONES_DE_PAGO' => ['valor' => '0'],
            'IMED_URL_INTERFAZ_PROD' => ['valor' => 'http://localhost'],
            'TIPO_FORMAS_PAGO_BONOS' => ['valor' => '1,2,3'],
        ];

        return $mockParameters[$parameterName] ?? ['valor' => '0'];
    }
}
