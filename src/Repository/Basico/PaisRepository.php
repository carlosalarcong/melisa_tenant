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
     * Resolver para obtener conexiones multi-tenant
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
     * INSERCIÓN CON DOCTRINE ORM
     * =========================
     * Utiliza el EntityManager para persistir la nueva entidad.
     * Maneja automáticamente el mapeo y las validaciones.
     * 
     * @param array $data Datos del país [nombrePais, nombreGentilicio, activo]
     * @return Pais Entidad País creada con ID asignado
     * @throws \Exception Si hay error en la creación
     */
    public function createPais(array $data): Pais
    {
        $entityManager = $this->getEntityManager();
        
        try {
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
     * CONSULTA AGREGADA
     * ================
     * Ejemplo de consulta con funciones agregadas usando DQL.
     * 
     * @return array Estadísticas [total, activos, inactivos]
     */
    public function getEstadisticas(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('
                COUNT(p.id) as total,
                SUM(CASE WHEN p.activo = true THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN p.activo = false THEN 1 ELSE 0 END) as inactivos
            ');

        $result = $qb->getQuery()->getSingleResult();

        return [
            'total' => (int) $result['total'],
            'activos' => (int) $result['activos'],
            'inactivos' => (int) $result['inactivos']
        ];
    }

    // ==========================================
    // MÉTODOS PARA MULTI-TENANT (FUTURO)
    // ==========================================

    /**
     * Obtiene EntityManager específico del tenant
     * 
     * INTEGRACIÓN MULTI-TENANT
     * ========================
     * Para futuras implementaciones donde se requiera
     * cambiar dinámicamente la conexión según el tenant.
     * 
     * @return EntityManagerInterface
     */
    private function getTenantEntityManager(): EntityManagerInterface
    {
        // TODO: Implementar cuando se requiera conexión dinámica por tenant
        // Por ahora usa el EntityManager por defecto
        return $this->getEntityManager();
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