<?php

namespace App\Controller;

use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controlador base para Mantenedores (Master Data)
 * 
 * Implementa el patrón Template Method para estandarizar el flujo CRUD
 * de todos los mantenedores del sistema.
 * 
 * Los mantenedores son datos maestros compartidos por todos los tenants
 * (países, géneros, religiones, etc.)
 * 
 * Beneficios:
 * - Reduce código duplicado en ~30%
 * - Flujo consistente en todos los mantenedores
 * - Fácil agregar nuevos mantenedores
 * - Centraliza cambios (modificar una vez, aplica a todos)
 * 
 * Uso:
 * ```php
 * class GenderController extends AbstractMantenedorController
 * {
 *     protected function getData(): array { return $this->repository->findAll(); }
 *     protected function getColumns(): array { return ['name', 'active']; }
 *     protected function getTemplatePath(): string { return 'mantenedores/basic/gender/index.html.twig'; }
 *     protected function getFormType(): string { return GenderType::class; }
 *     protected function createNewEntity(): object { return new Gender(); }
 * }
 * ```
 */
abstract class AbstractMantenedorController extends AbstractTenantAwareController
{
    protected TenantEntityManager $entityManager;
    protected ?ExportService $exportService = null;

    public function __construct(
        TenantEntityManager $entityManager,
        protected TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Inyecta el servicio de exportación (opcional)
     * Se usa setter injection para mantener retrocompatibilidad
     */
    public function setExportService(ExportService $exportService): void
    {
        $this->exportService = $exportService;
    }

    /**
     * Traduce un array de claves de columnas al idioma actual
     * 
     * Patrón funcional para traducir múltiples columnas de forma concisa.
     * Usa el dominio 'maintainers.columns' automáticamente.
     * 
     * @param array<string> $columns Claves de columnas (ej: ['name', 'code', 'is_active'])
     * @return array<string> Array de strings traducidos
     * 
     * @example
     * ```php
     * headers: $this->translateColumns(['code', 'name', 'is_active'])
     * // Resultado: ['Código', 'Nombre', 'Activo']
     * ```
     */
    protected function translateColumns(array $columns): array
    {
        return array_map(
            fn(string $column): string => $this->translator->trans(
                "maintainers.columns.$column",
                [],
                'maintainers'
            ),
            $columns
        );
    }

    /**
     * Detecta si la petición viene de un Turbo Frame
     * 
     * Turbo Frame puede enviar peticiones de dos formas:
     * 1. Con header Turbo-Frame cuando se hace submit de formulario
     * 2. Con Sec-Fetch-Dest: turboframe cuando se carga via src attribute
     */
    protected function isTurboFrameRequest(Request $request): bool
    {
        // Verificar header Turbo-Frame (para submits)
        if ($request->headers->has('Turbo-Frame')) {
            return true;
        }
        
        // Verificar Sec-Fetch-Dest (para lazy loading via src)
        if ($request->headers->get('Sec-Fetch-Dest') === 'turboframe') {
            return true;
        }
        
        return false;
    }

    /**
     * Template method: Define el flujo principal del listado
     * Las subclases llaman a este método desde sus rutas
     */
    protected function handleIndex(Request $request): Response
    {
        $this->beforeIndex($request);
        
        $dataOrQuery = $this->getData($request);
        
        // Auto-detección: QueryBuilder → Paginar | Array → Sin paginación
        if ($dataOrQuery instanceof QueryBuilder) {
            $pagination = $this->paginate($dataOrQuery, $request);
            $data = $pagination['items'];
            $paginationData = [
                'current_page' => $pagination['current_page'],
                'total_pages' => $pagination['total_pages'],
                'total_items' => $pagination['total_items'],
                'items_per_page' => $pagination['items_per_page'],
                'has_previous' => $pagination['has_previous'],
                'has_next' => $pagination['has_next'],
            ];
        } else {
            $data = $dataOrQuery;
            $paginationData = null;
        }
        
        $processedData = $this->processData($data, $request);
        
        $this->afterIndex($request);
        
        return $this->render($this->getTemplatePath(), [
            'data' => $processedData,
            'columns' => $this->getColumns(),
            'actions' => $this->getActions(),
            'tenant' => $this->getTenant(),
            'page_title' => $this->getPageTitle(),
            'create_route' => $this->getCreateRoute(),
            'is_turbo_frame' => $this->isTurboFrameRequest($request),
            'pagination' => $paginationData,
        ]);
    }

    /**
     * Template method: Define el flujo de creación de entidad
     * Las subclases llaman a este método desde sus rutas
     */
    protected function handleCreate(Request $request): Response
    {
        $this->beforeCreate($request);
        
        $entity = $this->createNewEntity();
        $form = $this->createForm($this->getFormType(), $entity);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->beforeSave($entity, $request);
            $this->save($entity);
            $this->afterSave($entity, $request);
            
            $this->addFlash('success', $this->getSuccessMessage('create'));
            
            // Siempre redirigir - el data-turbo-frame="_top" hace que salga del frame
            return $this->redirectToRoute($this->getIndexRoute());
        }
        
        // Si es una petición Turbo Frame, usar template modal
        $isTurbo = $this->isTurboFrameRequest($request);
        $template = $isTurbo 
            ? $this->getModalFormTemplatePath() 
            : $this->getFormTemplatePath();
        
        return $this->render($template, [
            'form' => $form->createView(),
            'entity' => $entity,
            'page_title' => $this->getPageTitle('create'),
            'cancel_route' => $this->getIndexRoute(),
            'is_turbo_frame' => $isTurbo,
            'action_url' => $this->generateUrl($this->getCreateRoute()),
        ]);
    }

    /**
     * Template method: Define el flujo de edición de entidad
     * Las subclases llaman a este método desde sus rutas
     */
    protected function handleEdit(Request $request, int $id): Response
    {
        $entity = $this->findEntity($id);
        
        if (!$entity) {
            $this->addFlash('error', $this->getErrorMessage('not_found'));
            return $this->redirectToRoute($this->getIndexRoute());
        }
        
        $this->beforeEdit($entity, $request);
        
        $form = $this->createForm($this->getFormType(), $entity);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->beforeSave($entity, $request);
            $this->save($entity);
            $this->afterSave($entity, $request);
            
            $this->addFlash('success', $this->getSuccessMessage('edit'));
            
            // Siempre redirigir - el data-turbo-frame="_top" hace que salga del frame
            return $this->redirectToRoute($this->getIndexRoute());
        }
        
        // Si es una petición Turbo Frame, usar template modal
        $template = $this->isTurboFrameRequest($request) 
            ? $this->getModalFormTemplatePath() 
            : $this->getFormTemplatePath();
        
        return $this->render($template, [
            'form' => $form->createView(),
            'entity' => $entity,
            'page_title' => $this->getPageTitle('edit'),
            'cancel_route' => $this->getIndexRoute(),
            'is_turbo_frame' => $this->isTurboFrameRequest($request),
            'action_url' => $this->generateUrl(str_replace('_index', '_edit', $this->getIndexRoute()), ['id' => $id]),
        ]);
    }

    /**
     * Template method: Define el flujo de eliminación de entidad
     * Las subclases llaman a este método desde sus rutas
     */
    protected function handleDelete(Request $request, int $id): Response
    {
        $entity = $this->findEntity($id);
        
        if (!$entity) {
            $this->addFlash('error', $this->getErrorMessage('not_found'));
            return $this->redirectToRoute($this->getIndexRoute());
        }
        
        if ($this->canDelete($entity)) {
            $this->beforeDelete($entity, $request);
            $this->remove($entity);
            $this->afterDelete($entity, $request);
            
            $this->addFlash('success', $this->getSuccessMessage('delete'));
        } else {
            $this->addFlash('error', $this->getErrorMessage('cannot_delete'));
        }
        
        return $this->redirectToRoute($this->getIndexRoute());
    }
    
    /**
     * Template method: Maneja la exportación de datos a CSV
     * 
     * Características:
     * - Alta performance con streaming (procesa chunks de 1000 registros)
     * - Bajo consumo de memoria (no carga todo en RAM)
     * - Reutilizable en todos los mantenedores
     * - Respeta filtros y ordenamiento actuales
     * 
     * Uso en controlador hijo:
     * ```php
     * #[Route('/export', name: 'app_maintainers_cost_center_export', methods: ['GET'])]
     * public function export(Request $request): Response
     * {
     *     return $this->handleExport($request);
     * }
     * ```
     * 
     * O personalizar columnas/headers:
     * ```php
     * return $this->handleExport(
     *     request: $request,
     *     columns: ['id', 'name', 'code'],
     *     headers: ['ID', 'Nombre', 'Código'],
     *     filename: 'centros_costo.csv'
     * );
     * ```
     */
    protected function handleExport(
        Request $request,
        ?array $columns = null,
        ?array $headers = null,
        ?string $filename = null
    ): Response {
        if (!$this->exportService) {
            throw new \RuntimeException('ExportService not injected. Add #[Autowire] or setter injection.');
        }
        
        // Obtener datos de la misma forma que el index (respeta filtros)
        $dataOrQuery = $this->getData($request);
        
        // Columnas por defecto desde getColumns()
        $columns = $columns ?? $this->getColumns();
        
        // Headers por defecto: capitalizar nombres de columnas
        $headers = $headers ?? array_map(fn($col) => ucfirst(str_replace('_', ' ', $col)), $columns);
        
        // Filename por defecto desde page title
        $filename = $filename ?? $this->sanitizeFilename($this->getPageTitle()) . '_' . date('Y-m-d') . '.csv';
        
        // Si es QueryBuilder, exportar con streaming
        if ($dataOrQuery instanceof QueryBuilder) {
            return $this->exportService->exportToCsv(
                queryBuilder: $dataOrQuery,
                columns: $columns,
                headers: $headers,
                filename: $filename
            );
        }
        
        // Si es array, exportar directamente
        return $this->exportService->exportArrayToCsv(
            data: $dataOrQuery,
            columns: $columns,
            headers: $headers,
            filename: $filename
        );
    }
    
    /**
     * Sanitiza nombre de archivo para export
     */
    private function sanitizeFilename(string $name): string
    {
        // Remover acentos
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        // Reemplazar espacios y caracteres especiales
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        // Remover guiones bajos múltiples
        $name = preg_replace('/_+/', '_', $name);
        return strtolower(trim($name, '_'));
    }

    // ========================================================================
    // HOOKS: Métodos que las subclases pueden sobreescribir
    // ========================================================================

    /**
     * Hook: Se ejecuta antes de listar
     */
    protected function beforeIndex(Request $request): void {}

    /**
     * Hook: Se ejecuta después de listar
     */
    protected function afterIndex(Request $request): void {}

    /**
     * Hook: Se ejecuta antes de crear
     */
    protected function beforeCreate(Request $request): void {}

    /**
     * Hook: Se ejecuta antes de editar
     */
    protected function beforeEdit(object $entity, Request $request): void {}

    /**
     * Hook: Se ejecuta antes de guardar (create y edit)
     */
    protected function beforeSave(object $entity, Request $request): void {}

    /**
     * Hook: Se ejecuta después de guardar (create y edit)
     */
    protected function afterSave(object $entity, Request $request): void {}

    /**
     * Hook: Se ejecuta antes de eliminar
     */
    protected function beforeDelete(object $entity, Request $request): void {}

    /**
     * Hook: Se ejecuta después de eliminar
     */
    protected function afterDelete(object $entity, Request $request): void {}

    // ========================================================================
    // MÉTODOS ABSTRACTOS: Las subclases DEBEN implementarlos
    // ========================================================================

    /**
     * Obtiene los datos a mostrar en el listado
     * 
     * Retornar QueryBuilder para paginación automática:
     *   return $this->repository->createQueryBuilder('e');
     * 
     * Retornar array para sin paginación:
     *   return $this->repository->findAll();
     * 
     * @return array|QueryBuilder Array de entidades o QueryBuilder para paginación
     */
    abstract protected function getData(Request $request): array|QueryBuilder;

    /**
     * Define las columnas a mostrar en la tabla
     * 
     * @return array Ejemplo: ['name', 'description', 'active']
     */
    abstract protected function getColumns(): array;

    /**
     * Ruta de la plantilla del listado
     * 
     * @return string Ejemplo: 'mantenedores/basic/gender/index.html.twig'
     */
    abstract protected function getTemplatePath(): string;

    /**
     * Tipo de formulario a usar
     * 
     * @return string Ejemplo: GenderType::class
     */
    abstract protected function getFormType(): string;

    /**
     * Crea una nueva instancia de la entidad
     * 
     * @return object Nueva instancia de la entidad
     */
    abstract protected function createNewEntity(): object;

    /**
     * Nombre de la ruta del index
     * 
     * @return string Ejemplo: 'app_maintainers_gender_index'
     */
    abstract protected function getIndexRoute(): string;

    /**
     * Título de la página usando traducciones automáticas por entidad.
     *
     * Genera claves de traducción basadas en el namespace y nombre de clase:
     * - maintainers.entities.{module}.{entity_key}.title
     * - maintainers.entities.{module}.{entity_key}.create_title
     * - maintainers.entities.{module}.{entity_key}.edit_title
     *
     * Ejemplo: CreditCardController en Treasury → maintainers.entities.treasury.credit_card.title
     */
    protected function getPageTitle(string $action = 'index'): string
    {
        $entityKey = $this->getEntityTranslationKey();

        return match($action) {
            'create' => $this->translator->trans(
                "maintainers.entities.{$entityKey}.create_title",
                [],
                'maintainers'
            ),
            'edit' => $this->translator->trans(
                "maintainers.entities.{$entityKey}.edit_title",
                [],
                'maintainers'
            ),
            default => $this->translator->trans(
                "maintainers.entities.{$entityKey}.title",
                [],
                'maintainers'
            )
        };
    }

    /**
     * Genera la clave de traducción de la entidad incluyendo el módulo.
     *
     * Ejemplo: App\Controller\Maintainers\Treasury\CreditCardController → treasury.credit_card
     */
    protected function getEntityTranslationKey(): string
    {
        $reflection = new \ReflectionClass($this);
        $namespace = $reflection->getNamespaceName();
        $className = $reflection->getShortName();

        // Extraer módulo del namespace: App\Controller\Maintainers\Treasury → treasury
        $module = '';
        if (preg_match('/Controller\\\\Maintainers\\\\(\w+)/', $namespace, $matches)) {
            $module = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $matches[1]));
        }

        // Extraer nombre de entidad: CreditCardController → credit_card
        $entityName = str_replace('Controller', '', $className);
        $entityKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $entityName));

        return $module ? "{$module}.{$entityKey}" : $entityKey;
    }

    // ========================================================================
    // MÉTODOS CON IMPLEMENTACIÓN POR DEFECTO: Pueden sobreescribirse
    // ========================================================================

    /**
     * Aplica paginación a un QueryBuilder
     * 
     * @param QueryBuilder $queryBuilder Query a paginar
     * @param Request $request Request con parámetro 'page'
     * @return array ['items' => array, 'current_page' => int, 'total_pages' => int, ...]
     */
    protected function paginate(QueryBuilder $queryBuilder, Request $request): array
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = $this->getItemsPerPage();
        
        $queryBuilder
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);
        
        $paginator = new Paginator($queryBuilder, fetchJoinCollection: true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $itemsPerPage));
        
        // Si la página solicitada excede el total, ajustar
        if ($page > $totalPages) {
            $page = $totalPages;
            $queryBuilder->setFirstResult(($page - 1) * $itemsPerPage);
            $paginator = new Paginator($queryBuilder, fetchJoinCollection: true);
        }
        
        return [
            'items' => iterator_to_array($paginator),
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'items_per_page' => $itemsPerPage,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }

    /**
     * Cantidad de items por página
     * Subclases pueden sobreescribir
     */
    protected function getItemsPerPage(): int
    {
        return 10;
    }

    /**
     * Procesa los datos antes de enviarlos a la vista
     * Por defecto no hace procesamiento
     */
    protected function processData(array $data, Request $request): array
    {
        return $data;
    }

    /**
     * Acciones disponibles en la tabla
     * Por defecto: view, edit, delete
     */
    protected function getActions(): array
    {
        return ['edit', 'delete'];
    }

    /**
     * Ruta de la plantilla del formulario
     * Por defecto usa el template base reutilizable
     */
    protected function getFormTemplatePath(): string
    {
        return 'maintainers/_base_form.html.twig';
    }

    /**
     * Ruta de la plantilla del formulario para modal
     * Se usa cuando la petición es un Turbo Frame
     */
    protected function getModalFormTemplatePath(): string
    {
        return 'maintainers/_modal_form.html.twig';
    }

    /**
     * Ruta para crear nueva entidad
     * Por defecto deriva del index route
     */
    protected function getCreateRoute(): string
    {
        return str_replace('_index', '_create', $this->getIndexRoute());
    }

    /**
     * Busca una entidad por ID
     * Subclases pueden sobreescribir para usar repository específico
     */
    protected function findEntity(int $id): ?object
    {
        $entityClass = $this->getEntityClass();
        return $this->entityManager->getRepository($entityClass)->find($id);
    }

    /**
     * Obtiene la clase de la entidad
     * Subclases deben sobreescribir si necesitan lógica custom
     */
    protected function getEntityClass(): string
    {
        $entity = $this->createNewEntity();
        return get_class($entity);
    }

    /**
     * Guarda la entidad
     */
    protected function save(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Elimina la entidad
     */
    protected function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * Verifica si se puede eliminar la entidad
     * Por defecto siempre permite, subclases pueden sobreescribir
     */
    protected function canDelete(object $entity): bool
    {
        return true;
    }

    /**
     * Mensajes de éxito
     */
    protected function getSuccessMessage(string $action): string
    {
        return match($action) {
            'create' => $this->translator->trans('maintainers.common.success_create', [], 'maintainers'),
            'edit' => $this->translator->trans('maintainers.common.success_update', [], 'maintainers'),
            'delete' => $this->translator->trans('maintainers.common.success_delete', [], 'maintainers'),
            default => $this->translator->trans('maintainers.common.success_create', [], 'maintainers')
        };
    }

    /**
     * Mensajes de error
     */
    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => $this->translator->trans('maintainers.common.error_update', [], 'maintainers'),
            'cannot_delete' => $this->translator->trans('maintainers.common.error_delete', [], 'maintainers'),
            default => $this->translator->trans('maintainers.common.error_create', [], 'maintainers')
        };
    }
}
