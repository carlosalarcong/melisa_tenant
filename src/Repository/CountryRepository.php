<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for country management
 */
class CountryRepository extends ServiceEntityRepository
{
    private Connection $connection;

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        $this->connection = $connection;
        // No parent constructor call because we don't have an entity
    }

    /**
     * Finds all active countries
     */
    public function findAllActive(): array
    {
        $sql = 'SELECT * FROM pais WHERE activo = 1 ORDER BY nombre_pais';
        return $this->connection->fetchAllAssociative($sql);
    }

    /**
     * Finds a country by ID
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM pais WHERE id = ?';
        $result = $this->connection->fetchAssociative($sql, [$id]);
        return $result ?: null;
    }

    /**
     * Finds all countries
     */
    public function findAll(): array
    {
        $sql = 'SELECT * FROM pais ORDER BY nombre_pais';
        return $this->connection->fetchAllAssociative($sql);
    }

    /**
     * Creates a new country
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO pais (nombre_pais, nombre_gentilicio, activo) VALUES (?, ?, ?)';
        $this->connection->executeStatement($sql, [
            $data['nombre_pais'] ?? $data['name'],
            $data['nombre_gentilicio'] ?? $data['demonym'],
            $data['activo'] ?? $data['isActive'] ? 1 : 0
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Updates an existing country
     */
    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE pais SET nombre_pais = ?, nombre_gentilicio = ?, activo = ? WHERE id = ?';
        $affected = $this->connection->executeStatement($sql, [
            $data['nombre_pais'] ?? $data['name'],
            $data['nombre_gentilicio'] ?? $data['demonym'],
            $data['activo'] ?? $data['isActive'] ? 1 : 0,
            $id
        ]);

        return $affected > 0;
    }

    /**
     * Deletes a country
     */
    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM pais WHERE id = ?';
        $affected = $this->connection->executeStatement($sql, [$id]);

        return $affected > 0;
    }
}