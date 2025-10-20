<?php

namespace App\Repository\Basico;

use App\Entity\Pais;
use App\Service\TenantResolver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repositorio para la gestión de Países
 * 
 * PATRÓN REPOSITORY CON DOCTRINE ORM
 * =================================
 * 
 * Este repositorio extiende ServiceEntityRepository de Doctrine para aprovechar
 * todas las funcionalidades del ORM mientras mantiene compatibilidad multi-tenant.
 * Combina las mejores prácticas de Doctrine con la arquitectura multi-tenant.
 * 
 * RESPONSABILIDADES:
 * - Operaciones CRUD usando Doctrine ORM
 * - Consultas específicas con QueryBuilder
 * - Manejo de conexiones multi-tenant
 * - Mapeo automático entidad ↔ base de datos
 * - Cache de consultas y optimizaciones ORM
 * 
 * VENTAJAS DE DOCTRINE ORM:
 * - Mapeo automático objeto-relacional
 * - QueryBuilder para consultas complejas
 * - Cache de segundo nivel
 * - Lazy loading de relaciones
 * - Validaciones automáticas
 * - Migraciones y esquemas
 * 
 * PATRÓN PARA NUEVOS REPOSITORIOS:
 * ===============================
 * 1. Duplica este archivo y ajusta namespace/clase
 * 2. Cambia la entidad en el constructor parent::__construct()
 * 3. Ajusta los métodos según tu entidad
 * 4. Mantén la integración multi-tenant
 * 5. Aprovecha QueryBuilder para consultas complejas
 * 6. Documenta consultas específicas de negocio
 * 
 * @author Equipo Melisa - Persistencia
 * @version 2.0 (Refactorizado con Doctrine ORM)
 * @since 2025-10-20
 * 
 * @extends ServiceEntityRepository<Pais>
 */
class PaisRepository extends ServiceEntityRepository
{
    /**
     * Resolver para obtener conexiones y contexto multi-tenant
     * 
     * FUNCIONES DEL TENANTRESOLVER EN REPOSITORY:
     * ==========================================
     * 1. Validación de acceso por tenant
     * 2. Auditoría de operaciones
     * 3. Contexto para estadísticas y reportes
     * 4. Información de conexiones DB por tenant
     * 5. Preparación para futuras conexiones dinámicas
     * 
     * @var TenantResolver
     */
    private TenantResolver $tenantResolver;

    /**
     * Constructor del repositorio
     * 
     * INTEGRACIÓN DOCTRINE + MULTI-TENANT
     * ===================================
     * Combina ServiceEntityRepository de Doctrine con TenantResolver
     * para mantener compatibilidad multi-tenant.
     * 
     * @param ManagerRegistry $registry Registry de Doctrine
     * @param TenantResolver $tenantResolver Resolver de tenant para conexiones DB
     */
    public function __construct(ManagerRegistry $registry, TenantResolver $tenantResolver)
    {
        parent::__construct($registry, Pais::class);
        $this->tenantResolver = $tenantResolver;
    }

    // ==========================================
    // MÉTODOS CRUD PRINCIPALES
    // ==========================================

    /**
     * Obtiene todos los países activos e inactivos
     * 
     * CONSULTA BASE CON DOCTRINE ORM
     * =============================
     * Utiliza el QueryBuilder de Doctrine para una consulta optimizada
     * con ordenamiento automático por nombre del país.
     * 
     * @return Pais[] Array de entidades País
     */
    public function findAllPaises(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.nombrePais', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtiene solo países activos
     * 
     * CONSULTA FILTRADA
     * ================
     * Útil para formularios donde solo se muestran países activos.
     * 
     * @return Pais[] Array de países activos
     */
    public function findActivePaises(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.activo = :activo')
            ->setParameter('activo', true)
            ->orderBy('p.nombrePais', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca un país por su ID
     * 
     * BÚSQUEDA POR CLAVE PRIMARIA
     * ===========================
     * Utiliza el método find() optimizado de Doctrine que incluye
     * cache de primer nivel automático.
     * 
     * @param int $id ID del país
     * @return Pais|null Entidad País o null si no existe
     */
    public function findPaisById(int $id): ?Pais
    {
        return $this->find($id);
    }

    /**
     * Crea un nuevo país
     * 
     * INSERCIÓN CON DOCTRINE ORM + TENANT CONTEXT
     * ===========================================
     * Utiliza el EntityManager para persistir la nueva entidad.
     * Incluye información del tenant para auditoría y validaciones.
     * 
     * @param array $data Datos del país [nombrePais, nombreGentilicio, activo]
     * @param string|null $tenantSlug Slug del tenant (para validación)
     * @return Pais Entidad País creada con ID asignado
     * @throws \Exception Si hay error en la creación
     */
    public function createPais(array $data, ?string $tenantSlug = null): Pais
    {
        $entityManager = $this->getEntityManager();
        
        try {
            // Validar acceso del tenant si se proporciona
            if ($tenantSlug && !$this->validateTenantAccess($tenantSlug)) {
                throw new \Exception('Tenant no autorizado para crear países');
            }
            
            // Log para auditoría usando TenantResolver
            if ($tenantSlug) {
                $tenantInfo = $this->tenantResolver->getTenantBySlug($tenantSlug);
                error_log(sprintf(
                    'AUDIT: Tenant "%s" creando país "%s"', 
                    $tenantInfo['name'] ?? $tenantSlug, 
                    $data['nombrePais']
                ));
            }

            // Crear nueva entidad
            $pais = new Pais();
            $pais->setNombrePais($data['nombrePais']);
            $pais->setNombreGentilicio($data['nombreGentilicio']);
            $pais->setActivo($data['activo'] ?? true);

            // Persistir en base de datos
            $entityManager->persist($pais);
            $entityManager->flush();

            return $pais;
            
        } catch (\Exception $e) {
            throw new \Exception('Error al crear país: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un país existente
     * 
     * ACTUALIZACIÓN CON DOCTRINE ORM
     * =============================
     * Utiliza el patrón de carga-modificación-guardado de Doctrine.
     * Automáticamente detecta cambios y optimiza las consultas UPDATE.
     * 
     * @param int $id ID del país a actualizar
     * @param array $data Datos actualizados
     * @return Pais Entidad actualizada
     * @throws \InvalidArgumentException Si el país no existe
     * @throws \Exception Si hay error en la actualización
     */
    public function updatePais(int $id, array $data): Pais
    {
        $entityManager = $this->getEntityManager();
        
        try {
            // Buscar entidad existente
            $pais = $this->find($id);
            if (!$pais) {
                throw new \InvalidArgumentException('País no encontrado');
            }

            // Actualizar campos
            $pais->setNombrePais($data['nombrePais']);
            $pais->setNombreGentilicio($data['nombreGentilicio']);
            $pais->setActivo($data['activo'] ?? true);

            // Guardar cambios (Doctrine detecta automáticamente los cambios)
            $entityManager->flush();

            return $pais;
            
        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception('Error al actualizar país: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un país por ID
     * 
     * ELIMINACIÓN CON DOCTRINE ORM
     * ===========================
     * Utiliza el EntityManager para eliminación segura.
     * Maneja automáticamente las claves foráneas según configuración.
     * 
     * @param int $id ID del país a eliminar
     * @return string Nombre del país eliminado
     * @throws \InvalidArgumentException Si el país no existe
     * @throws \Exception Si hay error en la eliminación
     */
    public function deletePais(int $id): string
    {
        $entityManager = $this->getEntityManager();
        
        try {
            // Buscar entidad existente
            $pais = $this->find($id);
            if (!$pais) {
                throw new \InvalidArgumentException('País no encontrado');
            }

            $nombrePais = $pais->getNombrePais();

            // Eliminar entidad
            $entityManager->remove($pais);
            $entityManager->flush();

            return $nombrePais;
            
        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception('Error al eliminar país: ' . $e->getMessage());
        }
    }

    // ==========================================
    // MÉTODOS DE CONSULTA ESPECÍFICOS
    // ==========================================

    /**
     * Busca países por nombre (búsqueda parcial)
     * 
     * BÚSQUEDA CON QUERYBUILDER
     * ========================
     * Ejemplo de consulta personalizada usando QueryBuilder
     * para búsquedas más complejas.
     * 
     * @param string $nombre Término de búsqueda
     * @return Pais[] Array de países que coinciden
     */
    public function findByNombre(string $nombre): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.nombrePais LIKE :nombre')
            ->setParameter('nombre', '%' . $nombre . '%')
            ->orderBy('p.nombrePais', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Verifica si existe un país con el nombre dado
     * 
     * VALIDACIÓN DE DUPLICADOS
     * ========================
     * Útil para validaciones de unicidad en el Service.
     * 
     * @param string $nombre Nombre del país a verificar
     * @param int|null $excludeId ID a excluir de la búsqueda (para updates)
     * @return bool True si existe, false si no
     */
    public function existsByNombre(string $nombre, ?int $excludeId = null): bool
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.nombrePais = :nombre')
            ->setParameter('nombre', $nombre);

        if ($excludeId) {
            $qb->andWhere('p.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Obtiene estadísticas de países
     * 
     * CONSULTA AGREGADA CON CONTEXTO TENANT
     * ====================================
     * Ejemplo de consulta con funciones agregadas usando DQL.
     * Incluye información del tenant para contexto y auditoría.
     * 
     * @param string|null $tenantSlug Slug del tenant para contexto
     * @return array Estadísticas [total, activos, inactivos, tenant_info]
     */
    public function getEstadisticas(?string $tenantSlug = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('
                COUNT(p.id) as total,
                SUM(CASE WHEN p.activo = true THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN p.activo = false THEN 1 ELSE 0 END) as inactivos
            ');

        $result = $qb->getQuery()->getSingleResult();

        $estadisticas = [
            'total' => (int) $result['total'],
            'activos' => (int) $result['activos'],
            'inactivos' => (int) $result['inactivos']
        ];

        // Agregar información del tenant usando TenantResolver
        if ($tenantSlug) {
            $tenantInfo = $this->tenantResolver->getTenantBySlug($tenantSlug);
            $estadisticas['tenant_info'] = [
                'name' => $tenantInfo['name'] ?? 'Desconocido',
                'slug' => $tenantSlug,
                'database' => $tenantInfo['database_name'] ?? 'default'
            ];
        }

        return $estadisticas;
    }

    // ==========================================
    // MÉTODOS PARA MULTI-TENANT (FUTURO)
    // ==========================================

    /**
     * Obtiene EntityManager específico del tenant
     * 
     * INTEGRACIÓN MULTI-TENANT
     * ========================
     * Utiliza el TenantResolver para obtener la conexión correcta
     * según el tenant actual de la sesión.
     * 
     * @return EntityManagerInterface
     */
    private function getTenantEntityManager(): EntityManagerInterface
    {
        // Por ahora usamos el EntityManager por defecto
        // En el futuro se puede implementar conexión dinámica:
        /*
        $tenantContext = $this->tenantResolver->getCurrentTenant();
        if ($tenantContext && isset($tenantContext['database_name'])) {
            $connection = $this->tenantResolver->createTenantConnection($tenantContext);
            return new EntityManager($connection, $this->getEntityManager()->getConfiguration());
        }
        */
        
        return $this->getEntityManager();
    }

    /**
     * Obtiene información del tenant actual
     * 
     * USO REAL DEL TENANTRESOLVER
     * ==========================
     * Ejemplo de cómo usar el TenantResolver para obtener
     * información del tenant desde la base de datos central.
     * 
     * @param string|null $slug Slug del tenant (opcional)
     * @return array|null Información del tenant
     */
    public function getTenantInfo(?string $slug = null): ?array
    {
        if (!$slug) {
            // En una implementación real, obtendríamos el slug del contexto actual
            // Por ahora devolvemos null si no se proporciona slug
            return null;
        }
        
        return $this->tenantResolver->getTenantBySlug($slug);
    }

    /**
     * Obtiene todos los tenants disponibles
     * 
     * USO DEL TENANTRESOLVER PARA LISTADOS
     * ===================================
     * Útil para selectores de tenant o reportes multi-tenant.
     * 
     * @return array Lista de tenants activos
     */
    public function getAllTenants(): array
    {
        return $this->tenantResolver->getAllActiveTenants();
    }

    /**
     * Valida si el tenant actual tiene acceso a los datos
     * 
     * VALIDACIÓN MULTI-TENANT
     * ======================
     * Método de ejemplo para validar permisos por tenant.
     * 
     * @param string $tenantSlug Slug del tenant a validar
     * @return bool True si tiene acceso, false si no
     */
    public function validateTenantAccess(string $tenantSlug): bool
    {
        $tenant = $this->tenantResolver->getTenantBySlug($tenantSlug);
        return $tenant !== null && ($tenant['is_active'] ?? false);
    }

    // ==========================================
    // MÉTODOS ADICIONALES PARA EXTENSIÓN
    // ==========================================

    /**
     * PLANTILLAS PARA CONSULTAS AVANZADAS
     * ===================================
     * 
     * // Búsqueda con paginación
     * public function findPaginatedPaises(int $page, int $limit): array
     * {
     *     return $this->createQueryBuilder('p')
     *         ->orderBy('p.nombrePais', 'ASC')
     *         ->setFirstResult(($page - 1) * $limit)
     *         ->setMaxResults($limit)
     *         ->getQuery()
     *         ->getResult();
     * }
     * 
     * // Consulta con JOIN
     * public function findPaisesWithRegiones(): array
     * {
     *     return $this->createQueryBuilder('p')
     *         ->leftJoin('p.regiones', 'r')
     *         ->addSelect('r')
     *         ->orderBy('p.nombrePais', 'ASC')
     *         ->getQuery()
     *         ->getResult();
     * }
     * 
     * // Consulta nativa SQL (cuando sea necesario)
     * public function findByCustomSQL(): array
     * {
     *     $sql = 'SELECT * FROM pais WHERE custom_condition = ?';
     *     $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
     *     $result = $stmt->executeQuery([1]);
     *     return $result->fetchAllAssociative();
     * }
     */
}