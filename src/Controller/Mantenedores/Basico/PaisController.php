<?php

namespace App\Controller\Mantenedores\Basico;

use App\Service\Basico\PaisService;
use App\Service\TenantContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Controlador para la gestión de Países
 * 
 * PATRÓN CONTROLLER - Capa de Control HTTP
 * =======================================
 * 
 * Este controlador maneja EXCLUSIVAMENTE las peticiones HTTP y respuestas.
 * NO contiene lógica de negocio (eso va en el Service) ni acceso a datos 
 * (eso va en el Repository). Su única responsabilidad es coordinar entre
 * la entrada HTTP y los servicios de aplicación.
 * 
 * RESPONSABILIDADES:
 * - Recibir y validar peticiones HTTP
 * - Extraer parámetros del request
 * - Invocar servicios de aplicación
 * - Formatear respuestas HTTP (JSON, HTML)
 * - Manejar errores HTTP y códigos de estado
 * - Gestionar autenticación y autorización
 * 
 * PATRÓN PARA NUEVOS MANTENEDORES:
 * ================================
 * 1. Duplica este archivo y ajusta namespace/clase
 * 2. Cambia la inyección del Service por el correspondiente
 * 3. Actualiza las rutas en los comentarios @Route
 * 4. Ajusta los templates en los métodos de vista
 * 5. Mantén la misma estructura de métodos CRUD
 * 6. Conserva el manejo de errores y respuestas JSON
 * 
 * MÉTODOS ESTÁNDAR PARA MANTENEDORES:
 * - index(): Vista principal (redirect a content)
 * - content(): Vista AJAX con datos
 * - list(): API JSON para listados
 * - create(): API para crear (POST)
 * - show(): API para obtener uno (GET)
 * - update(): API para actualizar (PUT/PATCH)
 * - delete(): API para eliminar (DELETE)
 * 
 * @author Equipo Melisa - Mantenedores
 * @version 1.0
 * @since 2025-10-20
 */
class PaisController extends AbstractController
{
    /**
     * Servicio de lógica de negocio para países
     * 
     * @var PaisService
     */
    private PaisService $paisService;
    
    /**
     * Contexto del tenant (para futuras implementaciones)
     * 
     * @var TenantContext
     */
    private TenantContext $tenantContext;
    
    /**
     * Manager para tokens CSRF de seguridad
     * 
     * @var CsrfTokenManagerInterface
     */
    private CsrfTokenManagerInterface $csrfTokenManager;

    /**
     * Constructor del controlador
     * 
     * INYECCIÓN DE DEPENDENCIAS
     * ========================
     * Todas las dependencias se inyectan via constructor usando
     * el sistema de autowiring de Symfony.
     * 
     * @param PaisService $paisService Servicio para lógica de países
     * @param TenantContext $tenantContext Contexto del tenant actual
     * @param CsrfTokenManagerInterface $csrfTokenManager Manager de tokens CSRF
     */
    public function __construct(
        PaisService $paisService,
        TenantContext $tenantContext,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->paisService = $paisService;
        $this->tenantContext = $tenantContext;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    // ==========================================
    // MÉTODOS DE VISTA (HTML)
    // ==========================================

    /**
     * Vista principal del mantenedor
     * 
     * RUTA: GET /mantenedores/basico/pais
     * 
     * VISTA PRINCIPAL PARA NAVEGACIÓN DIRECTA
     * =====================================
     * Este método maneja cuando alguien navega directamente a la URL principal.
     * Redirige al método content() para mantener consistencia con el sistema AJAX.
     * 
     * @return Response Respuesta de redirección
     */
    public function index(): Response
    {
        // Obtener tenant con fallback
        $tenant = $this->tenantContext->getCurrentTenant();
        if (!$tenant) {
            $tenant = [
                'id' => 1,
                'name' => 'Hospital Demo',
                'subdomain' => 'demo'
            ];
        }
        
        // Renderizar la página completa de mantenedores
        return $this->render('mantenedores/index.html.twig', [
            'tenant' => $tenant
        ]);
    }

    /**
     * Vista AJAX para cargar contenido dinámico
     * 
     * RUTA: GET /mantenedores/basico/pais/content
     * 
     * VISTA PRINCIPAL DEL MANTENEDOR
     * =============================
     * Este método renderiza el contenido principal que se carga via AJAX
     * en el sistema de mantenedores. Incluye la tabla de datos, formularios
     * y toda la interfaz de usuario.
     * 
     * @return Response Respuesta HTML con el contenido del mantenedor
     */
    public function content(): Response
    {
        try {
            // Obtener datos desde el servicio
            $paises = $this->paisService->getAllPaises();
            $tenant = $this->getCurrentTenant();
            
            // Renderizar vista con datos
            return $this->render('mantenedores/basico/pais/content.html.twig', [
                'mantenedor_config' => $this->getMantenedorConfig(),
                'tenant' => $tenant,
                'paises' => $paises,
                'csrf_token' => $this->csrfTokenManager->getToken('mantenedor_form')->getValue()
            ]);
            
        } catch (\Exception $e) {
            // En caso de error, mostrar vista con mensaje de error
            return $this->render('mantenedores/basico/pais/content.html.twig', [
                'mantenedor_config' => $this->getMantenedorConfig(),
                'tenant' => $this->getCurrentTenant(),
                'paises' => [],
                'error' => $e->getMessage(),
                'csrf_token' => $this->csrfTokenManager->getToken('mantenedor_form')->getValue()
            ]);
        }
    }

    // ==========================================
    // API ENDPOINTS (JSON)
    // ==========================================

    /**
     * API para listar países en formato JSON
     * 
     * RUTA: GET /mantenedores/basico/pais/list
     * 
     * ENDPOINT PARA CONSUMO VIA AJAX/API
     * =================================
     * Retorna la lista de países en formato JSON. Útil para
     * actualizaciones dinámicas o consumo desde JavaScript.
     * 
     * @return JsonResponse Lista de países en formato JSON
     */
    public function list(): JsonResponse
    {
        try {
            $paises = $this->paisService->getAllPaises();
            
            return new JsonResponse([
                'success' => true,
                'data' => $paises
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Error al cargar países: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Crear nuevo país
     * 
     * RUTA: POST /mantenedores/basico/pais
     * 
     * ENDPOINT DE CREACIÓN
     * ===================
     * Recibe datos via POST (JSON o form-data) y crea un nuevo país.
     * Valida datos, invoca el servicio y retorna respuesta JSON.
     * 
     * @param Request $request Petición HTTP con datos del país
     * @return JsonResponse Respuesta JSON con resultado de la operación
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Extraer datos del request
            $data = $this->extractRequestData($request);
            
            // Validar que se recibieron datos
            if (empty($data['nombrePais']) && empty($data['nombreGentilicio'])) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'No se recibieron datos válidos. Verifica el formulario.'
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Crear país via servicio (aplica validaciones y lógica de negocio)
            $pais = $this->paisService->createPais($data);
            
            // Respuesta exitosa
            return new JsonResponse([
                'success' => true,
                'message' => 'País creado exitosamente',
                'data' => $pais
            ], Response::HTTP_CREATED);
            
        } catch (\InvalidArgumentException $e) {
            // Error de validación (datos incorrectos)
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
            
        } catch (\Exception $e) {
            // Error interno del servidor
            return new JsonResponse([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener país por ID
     * 
     * RUTA: GET /mantenedores/basico/pais/{id}
     * 
     * ENDPOINT PARA OBTENER UN REGISTRO
     * ================================
     * Utilizado principalmente para cargar datos en formularios de edición.
     * Retorna los datos del país en formato JSON.
     * 
     * @param int $id ID del país a obtener
     * @return JsonResponse Datos del país o error si no existe
     */
    public function show(int $id): JsonResponse
    {
        try {
            // Obtener país via servicio
            $pais = $this->paisService->getPaisById($id);
            
            if (!$pais) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'País no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Respuesta exitosa
            return new JsonResponse([
                'success' => true,
                'data' => $pais
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualizar país existente
     * 
     * RUTA: PUT/PATCH /mantenedores/basico/pais/{id}
     * 
     * ENDPOINT DE ACTUALIZACIÓN
     * ========================
     * Recibe datos via PUT/PATCH y actualiza el país especificado.
     * Valida existencia, aplica validaciones y actualiza.
     * 
     * @param Request $request Petición HTTP con datos actualizados
     * @param int $id ID del país a actualizar
     * @return JsonResponse Resultado de la operación
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Extraer datos del request
            $data = $this->extractRequestData($request);
            
            // Actualizar país via servicio
            $pais = $this->paisService->updatePais($id, $data);
            
            // Respuesta exitosa
            return new JsonResponse([
                'success' => true,
                'message' => 'País actualizado exitosamente',
                'data' => $pais
            ]);
            
        } catch (\InvalidArgumentException $e) {
            // Error de validación o país no encontrado
            $statusCode = $e->getMessage() === 'País no encontrado' 
                ? Response::HTTP_NOT_FOUND 
                : Response::HTTP_BAD_REQUEST;
                
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], $statusCode);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar país
     * 
     * RUTA: DELETE /mantenedores/basico/pais/{id}
     * 
     * ENDPOINT DE ELIMINACIÓN
     * ======================
     * Elimina el país especificado aplicando validaciones de negocio.
     * Retorna confirmación con el nombre del país eliminado.
     * 
     * @param Request $request Petición HTTP
     * @param int $id ID del país a eliminar
     * @return JsonResponse Resultado de la operación
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        try {
            // Eliminar país via servicio (aplica validaciones de negocio)
            $nombrePais = $this->paisService->deletePais($id);
            
            // Respuesta exitosa con confirmación
            return new JsonResponse([
                'success' => true,
                'message' => "País \"$nombrePais\" eliminado exitosamente"
            ]);
            
        } catch (\InvalidArgumentException $e) {
            // País no encontrado
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\RuntimeException $e) {
            // Error de negocio (país en uso, etc.)
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_CONFLICT);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ==========================================
    // MÉTODOS PRIVADOS - UTILIDADES
    // ==========================================

    /**
     * Extrae datos del request HTTP
     * 
     * EXTRACCIÓN UNIFORME DE DATOS
     * ===========================
     * Maneja tanto JSON como form-data de manera transparente.
     * Normaliza los nombres de campos para el servicio.
     * 
     * @param Request $request Petición HTTP
     * @return array Datos extraídos y normalizados
     */
    private function extractRequestData(Request $request): array
    {
        // Intentar obtener datos JSON primero
        $content = $request->getContent();
        $data = null;
        
        if (!empty($content)) {
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $data = null;
            }
        }
        
        if (!$data || !is_array($data)) {
            // Si no hay JSON válido, obtener de form-data/POST
            $data = [
                'nombrePais' => trim($request->get('nombrePais', '')),
                'nombreGentilicio' => trim($request->get('nombreGentilicio', '')),
                'activo' => $request->get('activo') === 'on' 
                         || $request->get('activo') === '1' 
                         || $request->get('activo') === true
                         || $request->get('activo') === 'true'
            ];
        } else {
            // Normalizar datos JSON
            $data = [
                'nombrePais' => trim($data['nombrePais'] ?? ''),
                'nombreGentilicio' => trim($data['nombreGentilicio'] ?? ''),
                'activo' => $data['activo'] ?? true
            ];
        }
        
        return $data;
    }

    /**
     * Obtiene configuración del mantenedor
     * 
     * CONFIGURACIÓN DEL MANTENEDOR
     * ===========================
     * Define metadatos del mantenedor para uso en vistas.
     * Personaliza según las necesidades de tu entidad.
     * 
     * @return array Configuración del mantenedor
     */
    private function getMantenedorConfig(): array
    {
        return [
            'title' => 'Gestión de Países',
            'entity_name' => 'País',
            'entity_name_plural' => 'Países',
            'description' => 'Administra los países y nacionalidades del sistema',
            'icon' => 'fas fa-globe',
            'color' => 'primary'
        ];
    }

    /**
     * Obtiene información del tenant actual
     * 
     * CONTEXTO DEL TENANT INTEGRADO
     * =============================
     * Utiliza el TenantContext para obtener información del tenant actual.
     * Proporciona fallback para desarrollo cuando no hay sesión activa.
     * 
     * @return array Datos del tenant actual
     */
    private function getCurrentTenant(): array
    {
        // Intentar obtener tenant del contexto real
        $tenantData = $this->tenantContext->getCurrentTenant();
        
        if ($tenantData) {
            return [
                'name' => $tenantData['name'],
                'subdomain' => $tenantData['subdomain']
            ];
        }
        
        // Fallback para desarrollo
        return [
            'name' => 'Melisa Hospital (Dev)',
            'subdomain' => 'melisahospital'
        ];
    }

    // ==========================================
    // PLANTILLAS PARA EXTENSIÓN
    // ==========================================
    
    /**
     * MÉTODOS ADICIONALES PARA EXTENSIÓN
     * =================================
     * 
     * Aquí puedes agregar métodos específicos para tu mantenedor:
     * 
     * // Exportar a Excel/CSV
     * public function export(Request $request): Response
     * {
     *     $format = $request->get('format', 'excel');
     *     $data = $this->paisService->getAllPaises();
     *     // Implementar lógica de exportación
     * }
     * 
     * // Importar desde archivo
     * public function import(Request $request): JsonResponse
     * {
     *     $file = $request->files->get('file');
     *     // Implementar lógica de importación
     * }
     * 
     * // Búsqueda avanzada
     * public function search(Request $request): JsonResponse
     * {
     *     $termino = $request->get('q');
     *     $resultados = $this->paisService->searchPaises($termino);
     *     return new JsonResponse(['data' => $resultados]);
     * }
     * 
     * // Estadísticas del mantenedor
     * public function stats(): JsonResponse
     * {
     *     $stats = $this->paisService->getEstadisticasPaises();
     *     return new JsonResponse($stats);
     * }
     * 
     * // Activar/Desactivar en lote
     * public function bulkToggle(Request $request): JsonResponse
     * {
     *     $ids = $request->get('ids', []);
     *     $action = $request->get('action'); // 'activate' o 'deactivate'
     *     // Implementar lógica
     * }
     */
}