<?php

namespace App\Repository\Basico;

use Doctrine\DBAL\Connection;

class PaisRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $query = '
            SELECT id, nombre_pais, nombre_gentilicio, activo 
            FROM pais 
            ORDER BY nombre_pais ASC
        ';
        
        $result = $this->connection->executeQuery($query);
        return $result->fetchAllAssociative();
    }

    public function findById(int $id): ?array
    {
        $query = 'SELECT id, nombre_pais, nombre_gentilicio, activo FROM pais WHERE id = ?';
        $result = $this->connection->executeQuery($query, [$id]);
        $pais = $result->fetchAssociative();
        
        return $pais ?: null;
    }

    public function create(array $data): int
    {
        $query = '
            INSERT INTO pais (nombre_pais, nombre_gentilicio, activo) 
            VALUES (?, ?, ?)
        ';
        
        $this->connection->executeStatement($query, [
            $data['nombre_pais'],
            $data['nombre_gentilicio'],
            $data['activo'] ? 1 : 0
        ]);
        
        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $query = '
            UPDATE pais 
            SET nombre_pais = ?, nombre_gentilicio = ?, activo = ? 
            WHERE id = ?
        ';
        
        $rowsAffected = $this->connection->executeStatement($query, [
            $data['nombre_pais'],
            $data['nombre_gentilicio'],
            $data['activo'] ? 1 : 0,
            $id
        ]);
        
        return $rowsAffected > 0;
    }

    public function delete(int $id): bool
    {
        $query = 'DELETE FROM pais WHERE id = ?';
        $rowsAffected = $this->connection->executeStatement($query, [$id]);
        
        return $rowsAffected > 0;
    }

    public function exists(int $id): bool
    {
        $query = 'SELECT 1 FROM pais WHERE id = ? LIMIT 1';
        $result = $this->connection->executeQuery($query, [$id]);
        
        return $result->fetchOne() !== false;
    }
}