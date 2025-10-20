<?php

namespace App\Service\Basico;

use App\Entity\Pais;
use App\Repository\Basico\PaisRepository;
use App\Service\TenantResolver;
use App\Service\TenantContext;

/**
 * Servicio para la gestión de Países
 * 
 * PATRÓN SERVICE - Capa de Lógica de Negocio
 * ==========================================
 * 
 * Este servicio contiene toda la lógica de negocio relacionada con países.
 * Actúa como intermediario entre el Controller y el Repository, aplicando
 * validaciones, transformaciones y reglas de negocio.
 * 
 * RESPONSABILIDADES:
 * - Validar datos antes de enviarlos al Repository
 * - Aplicar reglas de negocio específicas del dominio
 * - Formatear datos para diferentes contextos (API, vistas, etc.)
 * - Manejar excepciones y errores de negocio
 * - Coordinar operaciones que involucren múltiples repositories
 * 
 * PATRÓN PARA NUEVOS MANTENEDORES:
 * 1. Duplica este archivo y cambia el namespace/clase
 * 2. Inyecta el Repository correspondiente
 * 3. Adapta los métodos de validación y formateo
 * 4. Mantén la estructura base de métodos públicos
 * 5. Agrega métodos específicos de tu dominio
 * 
 * @author Equipo Melisa - Mantenedores
 * @version 1.0
 * @since 2025-10-20
 */
class PaisService
{
    /**
     * Repository de países con Doctrine ORM
     * 
     * @var PaisRepository
     */
    private PaisRepository $paisRepository;

    /**
     * Resolver de tenant para conexiones multi-tenant
     * 
     * @var TenantResolver
     */
    private TenantResolver $tenantResolver;

    /**
     * Contexto del tenant actual
     * 
     * @var TenantContext
     */
    private TenantContext $tenantContext;

    /**
     * Constructor del servicio
     * 
     * INYECCIÓN DE DEPENDENCIAS CON DOCTRINE ORM
     * =========================================
     * Se inyecta el Repository que ahora extiende ServiceEntityRepository
     * de Doctrine, manteniendo compatibilidad multi-tenant.
     * 
     * @param PaisRepository $paisRepository Repository con Doctrine ORM
     * @param TenantResolver $tenantResolver Resolver para obtener conexión del tenant
     * @param TenantContext $tenantContext Contexto del tenant actual
     */
    public function __construct(
        PaisRepository $paisRepository,
        TenantResolver $tenantResolver,
        TenantContext $tenantContext
    ) {
        $this->paisRepository = $paisRepository;
        $this->tenantResolver = $tenantResolver;
        $this->tenantContext = $tenantContext;
    }

    // ==========================================
    // MÉTODOS PÚBLICOS - API DEL SERVICIO
    // ==========================================

    /**
     * Obtiene información del tenant actual para debugging
     * 
     * MÉTODO DE UTILIDAD PARA DESARROLLO
     * =================================
     * Útil para debugging y verificación del tenant actual.
     * 
     * @return array Información del tenant actual
     */
    public function getCurrentTenantInfo(): array
    {
        $tenantData = $this->getCurrentTenant();
        
        return [
            'tenant' => $tenantData,
            'hasRealTenant' => $this->tenantContext->hasCurrentTenant(),
            'usingFallback' => !$this->tenantContext->hasCurrentTenant(),
            'environment' => $_ENV['APP_ENV'] ?? 'unknown'
        ];
    }

    /**
     * Obtiene todos los países formateados para API
     * 
     * MÉTODO PARA APIs JSON
     * ====================
     * Este método es utilizado por controladores para respuestas API limpias.
     * 
     * @return array Array de países con formato para API
     * @throws \RuntimeException Si no se puede obtener la conexión del tenant
     */
    public function getAllPaisesForApi(): array
    {
        try {
            // Obtener entidades País desde el repository con Doctrine ORM
            $paisesEntidades = $this->paisRepository->findAllPaises();
            
            // Convertir entidades a arrays para la API
            return array_map([$this, 'formatPaisForApi'], $paisesEntidades);
            
        } catch (\Exception $e) {
            throw new \Exception('Error al obtener países para API: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los países formateados para vista
     * 
     * MÉTODO BASE PARA LISTADOS
     * ========================
     * Este método es utilizado por los controladores para mostrar datos
     * en tablas, grids o listados. Aplica formateo específico para la interfaz.
     * 
     * @return array Array de países con formato para vista:
     *               - id: int
     *               - nombrePais: string
     *               - nombreGentilicio: string
     *               - activo: bool
     *               - estadoTexto: string ("Activo"/"Inactivo")
     *               - estadoClase: string (clase CSS para badge)
     * @throws \RuntimeException Si no se puede obtener la conexión del tenant
     */
    public function getAllPaises(): array
    {
        try {
            // Obtener entidades País desde el repository con Doctrine ORM
            $paisesEntidades = $this->paisRepository->findAllPaises();
            
            // Convertir entidades a arrays para la API manteniendo compatibilidad
            return array_map([$this, 'formatPaisForView'], $paisesEntidades);
            
        } catch (\Exception $e) {
            throw new \Exception('Error al obtener países: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene un país por ID formateado para API
     * 
     * MÉTODO PARA CARGAR DATOS EN FORMULARIOS
     * ======================================
     * Utilizado principalmente para cargar datos en formularios de edición
     * o para mostrar detalles. Retorna formato limpio para APIs.
     * 
     * @param int $id ID del país a buscar
     * @return array|null Datos del país formateados para API o null si no existe
     * @throws \RuntimeException Si no se puede obtener la conexión del tenant
     */
    public function getPaisById(int $id): ?array
    {
        try {
            // Buscar entidad País por ID usando Doctrine ORM
            $paisEntidad = $this->paisRepository->findPaisById($id);
            
            if (!$paisEntidad) {
                return null;
            }
            
            return $this->formatPaisForApi($paisEntidad);
            
        } catch (\Exception $e) {
            throw new \Exception('Error al obtener país: ' . $e->getMessage());
        }
    }

    /**
     * Crea un nuevo país aplicando validaciones de negocio
     * 
     * MÉTODO DE CREACIÓN CON VALIDACIONES
     * ==================================
     * Aplica todas las validaciones necesarias antes de crear el registro.
     * Retorna los datos del país creado formateados.
     * 
     * @param array $data Datos del país a crear:
     *                    - nombrePais: string (requerido)
     *                    - nombreGentilicio: string (requerido)
     *                    - activo: bool (opcional, default: true)
     * @return array Datos del país creado con formato API
     * @throws \InvalidArgumentException Si los datos no pasan las validaciones
     * @throws \RuntimeException Si hay error en la creación
     */
    public function createPais(array $data): array
    {
        try {
            // Aplicar validaciones de negocio
            $this->validatePaisData($data);
            
            // Verificar duplicados usando Doctrine ORM
            if ($this->paisRepository->existsByNombre($data['nombrePais'])) {
                throw new \InvalidArgumentException('Ya existe un país con este nombre');
            }
            
            // Crear país usando Doctrine ORM
            $paisEntidad = $this->paisRepository->createPais($data);
            
            // Retornar datos formateados para API
            return $this->formatPaisForApi($paisEntidad);
            
        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception('Error al crear país: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un país existente aplicando validaciones
     * 
     * MÉTODO DE ACTUALIZACIÓN CON VALIDACIONES
     * =======================================
     * Verifica existencia, aplica validaciones y actualiza el registro.
     * 
     * @param int $id ID del país a actualizar
     * @param array $data Datos actualizados (misma estructura que create)
     * @return array Datos del país actualizado
     * @throws \InvalidArgumentException Si el país no existe o datos inválidos
     * @throws \RuntimeException Si hay error en la actualización
     */
    public function updatePais(int $id, array $data): array
    {
        try {
            // Aplicar validaciones de negocio
            $this->validatePaisData($data);
            
            // Verificar duplicados excluyendo el país actual
            if ($this->paisRepository->existsByNombre($data['nombrePais'], $id)) {
                throw new \InvalidArgumentException('Ya existe otro país con este nombre');
            }
            
            // Actualizar país usando Doctrine ORM
            $paisEntidad = $this->paisRepository->updatePais($id, $data);
            
            // Retornar datos formateados para API
            return $this->formatPaisForApi($paisEntidad);
            
        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception('Error al actualizar país: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un país aplicando validaciones de negocio
     * 
     * MÉTODO DE ELIMINACIÓN CON VALIDACIONES
     * =====================================
     * Verifica existencia y aplica reglas de negocio antes de eliminar.
     * Aquí puedes agregar validaciones como verificar si el país está
     * siendo usado en otras tablas (ciudades, clientes, etc.).
     * 
     * @param int $id ID del país a eliminar
     * @return string Nombre del país eliminado (para confirmación)
     * @throws \InvalidArgumentException Si el país no existe
     * @throws \RuntimeException Si hay error en la eliminación o está en uso
     */
    public function deletePais(int $id): string
    {
        try {
            // Verificar si se puede eliminar (reglas de negocio)
            $this->validatePaisDeletion($id);
            
            // Eliminar país usando Doctrine ORM
            return $this->paisRepository->deletePais($id);
            
        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Exception('Error al eliminar país: ' . $e->getMessage());
        }
    }

    // ==========================================
    // MÉTODOS PRIVADOS - LÓGICA INTERNA
    // ==========================================

    /**
     * Obtiene una instancia del repository con conexión del tenant actual
     * 
     * FACTORY METHOD PARA REPOSITORY
     * =============================
     * Crea el repository con la conexión específica del tenant.
     * Este patrón permite que el mismo código funcione para diferentes tenants.
     * 
     * @return PaisRepository Repository configurado para el tenant actual
     * @throws \RuntimeException Si no se puede resolver el tenant actual
     */
    private function getRepository(): PaisRepository
    {
        // Obtener tenant actual (esto debería venir del contexto en producción)
        $tenant = $this->getCurrentTenant();
        
        if (!$tenant) {
            throw new \RuntimeException('No se pudo resolver el tenant actual');
        }
        
        // Crear conexión específica del tenant
        $connection = $this->tenantResolver->createTenantConnection($tenant);
        
        return new PaisRepository($connection);
    }
    
    /**
     * Obtiene información del tenant actual
     * 
     * INTEGRACIÓN CON TENANTCONTEXT REAL
     * =================================
     * Utiliza el TenantContext para obtener información del tenant actual
     * desde la sesión o el contexto de la request. Proporciona fallback
     * para entornos de desarrollo.
     * 
     * @return array|null Datos del tenant actual
     * @throws \RuntimeException Si no se puede resolver el tenant en producción
     */
    private function getCurrentTenant(): ?array
    {
        // Obtener tenant del contexto real
        $tenantData = $this->tenantContext->getCurrentTenant();
        
        if ($tenantData) {
            return $tenantData;
        }
        
        // Obtener entorno actual - si no está definido, asumimos desarrollo
        $environment = $_ENV['APP_ENV'] ?? null;
        
        // Fallback para desarrollo/testing cuando no hay sesión activa
        if ($environment === null || $environment === 'dev' || $environment === 'test') {
            return [
                'id' => 1,
                'name' => 'Melisa Hospital (Dev)',
                'subdomain' => 'melisahospital',
                'database_name' => 'melisahospital',
                'host' => 'localhost',
                'host_port' => 3306,
                'db_user' => 'melisa',
                'db_password' => 'melisamelisa',
                'is_active' => 1
            ];
        }
        
        // En producción, si no hay tenant, es un error crítico
        throw new \RuntimeException('No se pudo resolver el tenant actual. Verifique que el usuario esté autenticado.');
    }

    /**
     * Valida los datos de entrada para países
     * 
     * VALIDACIONES DE NEGOCIO
     * ======================
     * Aplica todas las reglas de validación específicas para países.
     * Puedes extender estas validaciones según las necesidades del negocio.
     * 
     * @param array $data Datos a validar
     * @throws \InvalidArgumentException Si alguna validación falla
     */
    private function validatePaisData(array $data): void
    {
        // Validación: Nombre del país es obligatorio
        if (empty($data['nombrePais']) || trim($data['nombrePais']) === '') {
            throw new \InvalidArgumentException('El nombre del país es obligatorio');
        }
        
        // Validación: Gentilicio es obligatorio
        if (empty($data['nombreGentilicio']) || trim($data['nombreGentilicio']) === '') {
            throw new \InvalidArgumentException('El gentilicio es obligatorio');
        }
        
        // Validación: Longitud máxima del nombre
        if (strlen(trim($data['nombrePais'])) > 255) {
            throw new \InvalidArgumentException('El nombre del país no puede superar 255 caracteres');
        }
        
        // Validación: Longitud máxima del gentilicio
        if (strlen(trim($data['nombreGentilicio'])) > 255) {
            throw new \InvalidArgumentException('El gentilicio no puede superar 255 caracteres');
        }
        
        // VALIDACIONES ADICIONALES POSIBLES:
        // - Verificar que no exista otro país con el mismo nombre
        // - Validar formato de caracteres (solo letras y espacios)
        // - Validar palabras prohibidas
        // - etc.
    }

    /**
     * Valida si un país se puede eliminar
     * 
     * VALIDACIONES DE ELIMINACIÓN CON DOCTRINE ORM
     * ===========================================
     * Verifica reglas de negocio antes de permitir la eliminación.
     * Útil para mantener integridad referencial y reglas de negocio.
     * 
     * @param int $id ID del país a eliminar
     * @throws \InvalidArgumentException Si el país no existe
     * @throws \RuntimeException Si no se puede eliminar por reglas de negocio
     */
    private function validatePaisDeletion(int $id): void
    {
        // Verificar que el país existe
        $pais = $this->paisRepository->findPaisById($id);
        if (!$pais) {
            throw new \InvalidArgumentException('País no encontrado');
        }

        // TODO: Agregar validaciones de negocio específicas
        // Ejemplo: verificar si tiene regiones asociadas
        // if (!empty($pais->getRegiones())) {
        //     throw new \RuntimeException('No se puede eliminar el país porque tiene regiones asociadas');
        // }
        
        // Ejemplo: verificar si hay usuarios de este país
        // if ($this->hasUsersFromCountry($id)) {
        //     throw new \RuntimeException('No se puede eliminar el país porque hay usuarios asociados');
        // }
    }

        /**
     * Formatea datos de país para mostrar en vistas
     * 
     * FORMATEO PARA INTERFAZ CON DOCTRINE ORM
     * ======================================
     * Convierte una entidad Pais de Doctrine a array formateado
     * para usar en tablas y vistas del frontend.
     * 
     * @param Pais $pais Entidad País de Doctrine
     * @return array Datos formateados para vista
     */
    private function formatPaisForView(Pais $pais): array
    {
        return [
            'idPais' => $pais->getId(),
            'nombrePais' => $pais->getNombrePais() ?? 'Sin nombre',
            'nombreGentilicio' => $pais->getNombreGentilicio() ?? 'Sin gentilicio',
            'activo' => (bool) $pais->getActivo(),
            'estadoTexto' => $pais->getActivo() ? 'Activo' : 'Inactivo',
            'estadoClase' => $pais->getActivo() ? 'bg-success' : 'bg-secondary'
        ];
    }

        /**
     * Formatea un país para APIs (JSON responses)
     * 
     * FORMATEO PARA APIs CON DOCTRINE ORM
     * ==================================
     * Convierte una entidad Pais a array limpio para respuestas JSON.
     * Formato específico para APIs REST y endpoints AJAX.
     * 
     * @param Pais $pais Entidad País de Doctrine
     * @return array Datos formateados para API
     */
    private function formatPaisForApi(Pais $pais): array
    {
        return [
            'idPais' => $pais->getId(),
            'nombrePais' => $pais->getNombrePais(),
            'nombreGentilicio' => $pais->getNombreGentilicio(),
            'activo' => $pais->getActivo()
        ];
    }

    // ==========================================
    // MÉTODOS DE EXTENSIÓN - PARA IMPLEMENTAR
    // ==========================================
    
    /**
     * PLANTILLA PARA MÉTODOS ADICIONALES
     * =================================
     * 
     * Aquí puedes agregar métodos específicos para tu dominio:
     * 
     * // Buscar países activos únicamente
     * public function getPaisesActivos(): array
     * {
     *     $repository = $this->getRepository();
     *     // Implementar lógica específica
     * }
     * 
     * // Verificar si un país tiene datos relacionados (ciudades, etc.)
     * private function paisHasRelatedData(int $paisId): bool
     * {
     *     // Verificar tablas relacionadas
     *     // return count > 0;
     * }
     * 
     * // Búsqueda por término
     * public function searchPaises(string $termino): array
     * {
     *     // Implementar búsqueda por nombre o gentilicio
     * }
     * 
     * // Estadísticas
     * public function getEstadisticasPaises(): array
     * {
     *     // Total, activos, inactivos, etc.
     * }
     */
}