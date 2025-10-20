<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository para manejo de países
 */
class PaisRepository extends ServiceEntityRepository
{
    private Connection $connection;

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        $this->connection = $connection;
        // No llamamos al constructor padre porque no tenemos entidad
    }

    /**
     * Encuentra todos los países activos
     */
    public function findAllActive(): array
    {
        $sql = 'SELECT * FROM pais WHERE activo = 1 ORDER BY nombre_pais';
        return $this->connection->fetchAllAssociative($sql);
    }

    /**
     * Encuentra un país por ID
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM pais WHERE id = ?';
        $result = $this->connection->fetchAssociative($sql, [$id]);
        return $result ?: null;
    }

    /**
     * Encuentra todos los países
     */
    public function findAll(): array
    {
        $sql = 'SELECT * FROM pais ORDER BY nombre_pais';
        return $this->connection->fetchAllAssociative($sql);
    }

    /**
     * Crea un nuevo país
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO pais (nombre_pais, nombre_gentilicio, activo) VALUES (?, ?, ?)';
        $this->connection->executeStatement($sql, [
            $data['nombre_pais'] ?? $data['nombrePais'],
            $data['nombre_gentilicio'] ?? $data['nombreGentilicio'],
            $data['activo'] ? 1 : 0
        ]);
        
        return (int) $this->connection->lastInsertId();
    }

    /**
     * Actualiza un país existente
     */
    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE pais SET nombre_pais = ?, nombre_gentilicio = ?, activo = ? WHERE id = ?';
        $affected = $this->connection->executeStatement($sql, [
            $data['nombre_pais'] ?? $data['nombrePais'],
            $data['nombre_gentilicio'] ?? $data['nombreGentilicio'],
            $data['activo'] ? 1 : 0,
            $id
        ]);
        
        return $affected > 0;
    }

    /**
     * Elimina un país
     */
    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM pais WHERE id = ?';
        $affected = $this->connection->executeStatement($sql, [$id]);
        
        return $affected > 0;
    }
}