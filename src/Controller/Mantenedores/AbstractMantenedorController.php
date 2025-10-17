<?php

namespace App\Controller\Mantenedores;

use App\Controller\AbstractTenantController;
use App\Service\TenantContext;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Controlador base abstracto para todos los mantenedores
 * Proporciona funcionalidad común para CRUD con AJAX
 */
abstract class AbstractMantenedorController extends AbstractTenantController
{
    protected EntityManagerInterface $entityManager;
    protected ValidatorInterface $validator;
    protected CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        TenantContext $tenantContext,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        parent::__construct($tenantContext);
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Obtiene el nombre de la entidad manejada por este controlador
     */
    abstract protected function getEntityClass(): string;

    /**
     * Obtiene el repositorio para la entidad
     */
    protected function getRepository()
    {
        return $this->entityManager->getRepository($this->getEntityClass());
    }

    /**
     * Crea una nueva instancia de la entidad
     */
    abstract protected function createEntity(): object;

    /**
     * Mapea los datos del request a la entidad
     */
    abstract protected function mapRequestToEntity(Request $request, object $entity): void;

    /**
     * Obtiene el nombre del template específico para la entidad
     */
    abstract protected function getTemplateName(): string;

    /**
     * Obtiene la configuración específica del mantenedor
     */
    abstract protected function getMantenedorConfig(): array;

    /**
     * Vista principal del mantenedor
     */
    public function index(): Response
    {
        $config = $this->getMantenedorConfig();
        $templateName = $this->getTemplateName();
        
        return $this->render($templateName, [
            'mantenedor_config' => $config,
            'tenant' => $this->getCurrentTenant(),
            'csrf_token' => $this->csrfTokenManager->getToken('mantenedor_form')->getValue()
        ]);
    }

    /**
     * Lista paginada con búsqueda y ordenamiento - AJAX
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $page = max(1, (int) $request->query->get('page', 1));
            $limit = min(100, max(10, (int) $request->query->get('limit', 20)));
            $search = trim($request->query->get('search', ''));
            $orderBy = $request->query->get('orderBy', 'id');
            $orderDir = $request->query->get('orderDir', 'ASC');

            // Validar dirección de orden
            $orderDir = in_array(strtoupper($orderDir), ['ASC', 'DESC']) ? strtoupper($orderDir) : 'ASC';

            $repository = $this->getRepository();
            
            // Construir query con búsqueda y ordenamiento
            $queryBuilder = $repository->createQueryBuilder('e');
            
            // Aplicar búsqueda si hay término
            if (!empty($search)) {
                $this->applySearchFilter($queryBuilder, $search);
            }

            // Aplicar ordenamiento
            $queryBuilder->orderBy("e.{$orderBy}", $orderDir);

            // Contar total de registros
            $totalQuery = clone $queryBuilder;
            $totalQuery->select('COUNT(e.id)');
            $total = $totalQuery->getQuery()->getSingleScalarResult();

            // Aplicar paginación
            $offset = ($page - 1) * $limit;
            $queryBuilder->setFirstResult($offset)->setMaxResults($limit);

            $entities = $queryBuilder->getQuery()->getResult();

            // Convertir entities a array para JSON
            $data = array_map([$this, 'entityToArray'], $entities);

            return new JsonResponse([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit),
                    'showing' => [
                        'from' => $offset + 1,
                        'to' => min($offset + $limit, $total),
                        'total' => $total
                    ]
                ],
                'search' => $search,
                'orderBy' => $orderBy,
                'orderDir' => $orderDir
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al cargar datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene una entidad específica - AJAX
     */
    public function show(int $id): JsonResponse
    {
        try {
            $entity = $this->getRepository()->find($id);
            
            if (!$entity) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $this->entityToArray($entity)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al obtener registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea nueva entidad - AJAX
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Validar CSRF token
            if (!$this->isCsrfTokenValid('mantenedor_form', $request->request->get('_token'))) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Token de seguridad inválido'
                ], 400);
            }

            $entity = $this->createEntity();
            $this->mapRequestToEntity($request, $entity);

            // Validar entidad
            $violations = $this->validator->validate($entity);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }

                return new JsonResponse([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $errors
                ], 400);
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Registro creado exitosamente',
                'data' => $this->entityToArray($entity)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al crear registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza entidad existente - AJAX
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Validar CSRF token
            if (!$this->isCsrfTokenValid('mantenedor_form', $request->request->get('_token'))) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Token de seguridad inválido'
                ], 400);
            }

            $entity = $this->getRepository()->find($id);
            
            if (!$entity) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);
            }

            $this->mapRequestToEntity($request, $entity);

            // Validar entidad
            $violations = $this->validator->validate($entity);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }

                return new JsonResponse([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $errors
                ], 400);
            }

            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Registro actualizado exitosamente',
                'data' => $this->entityToArray($entity)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al actualizar registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina entidad - AJAX
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        try {
            // Validar CSRF token
            if (!$this->isCsrfTokenValid('mantenedor_delete', $request->request->get('_token'))) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Token de seguridad inválido'
                ], 400);
            }

            $entity = $this->getRepository()->find($id);
            
            if (!$entity) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);
            }

            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Registro eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al eliminar registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aplica filtros de búsqueda al QueryBuilder
     * Override en controllers específicos para personalizar búsqueda
     */
    protected function applySearchFilter($queryBuilder, string $search): void
    {
        // Implementación básica - buscar por nombre/descripción
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('e.nombre', ':search'),
                $queryBuilder->expr()->like('e.descripcion', ':search')
            )
        )->setParameter('search', "%{$search}%");
    }

    /**
     * Convierte entidad a array para respuesta JSON
     * Override en controllers específicos para personalizar salida
     */
    protected function entityToArray(object $entity): array
    {
        return [
            'id' => $entity->getId(),
            'nombre' => method_exists($entity, 'getNombre') ? $entity->getNombre() : '',
            'descripcion' => method_exists($entity, 'getDescripcion') ? $entity->getDescripcion() : '',
            'activo' => method_exists($entity, 'isActivo') ? $entity->isActivo() : true,
            'created_at' => method_exists($entity, 'getCreatedAt') ? $entity->getCreatedAt()?->format('Y-m-d H:i:s') : null
        ];
    }
}