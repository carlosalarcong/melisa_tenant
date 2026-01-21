<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Template method: Define el flujo principal del listado
     * Las subclases llaman a este método desde sus rutas
     */
    protected function handleIndex(Request $request): Response
    {
        $this->beforeIndex($request);
        
        $data = $this->getData($request);
        $processedData = $this->processData($data, $request);
        
        $this->afterIndex($request);
        
        return $this->render($this->getTemplatePath(), [
            'data' => $processedData,
            'columns' => $this->getColumns(),
            'actions' => $this->getActions(),
            'tenant' => $this->getTenant(),
            'page_title' => $this->getPageTitle(),
            'create_route' => $this->getCreateRoute(),
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
            
            return $this->redirectToRoute($this->getIndexRoute());
        }
        
        return $this->render($this->getFormTemplatePath(), [
            'form' => $form->createView(),
            'entity' => $entity,
            'page_title' => $this->getPageTitle('create'),
            'cancel_route' => $this->getIndexRoute(),
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
            
            return $this->redirectToRoute($this->getIndexRoute());
        }
        
        return $this->render($this->getFormTemplatePath(), [
            'form' => $form->createView(),
            'entity' => $entity,
            'page_title' => $this->getPageTitle('edit'),
            'cancel_route' => $this->getIndexRoute(),
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
     * @return array Array de entidades
     */
    abstract protected function getData(Request $request): array;

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
     * Título de la página
     * 
     * @param string $action Acción: 'index', 'create', 'edit'
     * @return string Ejemplo: 'Gender Management'
     */
    abstract protected function getPageTitle(string $action = 'index'): string;

    // ========================================================================
    // MÉTODOS CON IMPLEMENTACIÓN POR DEFECTO: Pueden sobreescribirse
    // ========================================================================

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
            'create' => 'Record created successfully',
            'edit' => 'Record updated successfully',
            'delete' => 'Record deleted successfully',
            default => 'Operation completed successfully'
        };
    }

    /**
     * Mensajes de error
     */
    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Record not found',
            'cannot_delete' => 'Cannot delete this record',
            default => 'An error occurred'
        };
    }
}
