<?php

namespace App\Service\Basico;

use App\Repository\Basico\PaisRepository;
use App\Service\TenantResolver;

class PaisService
{
    private TenantResolver $tenantResolver;

    public function __construct(TenantResolver $tenantResolver)
    {
        $this->tenantResolver = $tenantResolver;
    }

    private function getRepository(): PaisRepository
    {
        // Obtener tenant actual (esto debería venir del contexto)
        $tenant = $this->getCurrentTenant();
        
        if (!$tenant) {
            throw new \RuntimeException('No se pudo resolver el tenant actual');
        }
        
        $connection = $this->tenantResolver->createTenantConnection($tenant);
        return new PaisRepository($connection);
    }
    
    private function getCurrentTenant(): ?array
    {
        // Por ahora, hardcodeado para melisahospital
        // En producción esto debería venir del TenantContext
        return [
            'id' => 1,
            'name' => 'Melisa Hospital',
            'subdomain' => 'melisahospital',
            'database_name' => 'melisahospital',
            'host' => 'localhost',
            'host_port' => 3306,
            'db_user' => 'melisa',
            'db_password' => 'melisamelisa',
            'is_active' => 1
        ];
    }

    public function getAllPaises(): array
    {
        $repository = $this->getRepository();
        $paises = $repository->findAll();
        
        return array_map([$this, 'formatPaisForView'], $paises);
    }

    public function getPaisById(int $id): ?array
    {
        $repository = $this->getRepository();
        $pais = $repository->findById($id);
        
        if (!$pais) {
            return null;
        }
        
        return $this->formatPaisForApi($pais);
    }

    public function createPais(array $data): array
    {
        $this->validatePaisData($data);
        
        $repository = $this->getRepository();
        $paisData = [
            'nombre_pais' => $data['nombrePais'],
            'nombre_gentilicio' => $data['nombreGentilicio'],
            'activo' => $data['activo'] ?? true
        ];
        
        $id = $repository->create($paisData);
        
        return [
            'id' => $id,
            'nombrePais' => $paisData['nombre_pais'],
            'nombreGentilicio' => $paisData['nombre_gentilicio'],
            'activo' => $paisData['activo']
        ];
    }

    public function updatePais(int $id, array $data): array
    {
        $repository = $this->getRepository();
        
        if (!$repository->exists($id)) {
            throw new \InvalidArgumentException('País no encontrado');
        }
        
        $this->validatePaisData($data);
        
        $paisData = [
            'nombre_pais' => $data['nombrePais'],
            'nombre_gentilicio' => $data['nombreGentilicio'],
            'activo' => $data['activo'] ?? true
        ];
        
        $repository->update($id, $paisData);
        
        return [
            'id' => $id,
            'nombrePais' => $paisData['nombre_pais'],
            'nombreGentilicio' => $paisData['nombre_gentilicio'],
            'activo' => $paisData['activo']
        ];
    }

    public function deletePais(int $id): string
    {
        $repository = $this->getRepository();
        
        $pais = $repository->findById($id);
        if (!$pais) {
            throw new \InvalidArgumentException('País no encontrado');
        }
        
        $nombrePais = $pais['nombre_pais'];
        
        if (!$repository->delete($id)) {
            throw new \RuntimeException('Error al eliminar el país');
        }
        
        return $nombrePais;
    }

    private function validatePaisData(array $data): void
    {
        if (empty($data['nombrePais'])) {
            throw new \InvalidArgumentException('El nombre del país es obligatorio');
        }
        
        if (empty($data['nombreGentilicio'])) {
            throw new \InvalidArgumentException('El gentilicio es obligatorio');
        }
    }

    private function formatPaisForView(array $pais): array
    {
        return [
            'id' => $pais['id'],
            'nombrePais' => $pais['nombre_pais'] ?? 'Sin nombre',
            'nombreGentilicio' => $pais['nombre_gentilicio'] ?? 'Sin gentilicio',
            'activo' => (bool) $pais['activo'],
            'estadoTexto' => $pais['activo'] ? 'Activo' : 'Inactivo',
            'estadoClase' => $pais['activo'] ? 'bg-success' : 'bg-secondary'
        ];
    }

    private function formatPaisForApi(array $pais): array
    {
        return [
            'id' => $pais['id'],
            'nombrePais' => $pais['nombre_pais'],
            'nombreGentilicio' => $pais['nombre_gentilicio'],
            'activo' => (bool) $pais['activo']
        ];
    }
}