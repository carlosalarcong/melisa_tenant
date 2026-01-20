<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\Religion;
use App\Form\Maintainers\ReligionType;
use App\Repository\Tenant\ReligionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Religion Controller
 * 
 * Gestiona el mantenedor de Religiones
 * Las religiones son datos compartidos por todos los tenants
 */
#[Route('/maintainers/basic/religion')]
class ReligionController extends AbstractMantenedorController
{
    public function __construct(
        private ReligionRepository $religionRepository,
        $entityManager
    ) {
        parent::__construct($entityManager);
    }

    #[Route('', name: 'app_maintainers_religion_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return parent::index($request);
    }

    #[Route('/create', name: 'app_maintainers_religion_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return parent::create($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_religion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return parent::edit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_religion_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return parent::delete($request, $id);
    }

    // ========================================================================
    // Implementación de métodos abstractos
    // ========================================================================

    protected function getData(Request $request): array
    {
        // Todas las religiones - sin filtrar por tenant (dato maestro compartido)
        return $this->religionRepository->findAll();
    }

    protected function getColumns(): array
    {
        return ['name', 'code', 'active'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/basic/religion/index.html.twig';
    }

    protected function getFormType(): string
    {
        return ReligionType::class;
    }

    protected function createNewEntity(): object
    {
        return new Religion();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_religion_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Create Religion',
            'edit' => 'Edit Religion',
            default => 'Religion Management'
        };
    }

    // ========================================================================
    // Hooks personalizados (opcional)
    // ========================================================================

    /**
     * Normalizar el código a mayúsculas antes de guardar
     */
    protected function beforeSave(object $entity, Request $request): void
    {
        if ($entity instanceof Religion && $entity->getCode()) {
            $entity->setCode(strtoupper($entity->getCode()));
        }
        
        $entity->setUpdatedAt(new \DateTime());
    }

    /**
     * Mensajes personalizados en español
     */
    protected function getSuccessMessage(string $action): string
    {
        return match($action) {
            'create' => 'Religión creada exitosamente',
            'edit' => 'Religión actualizada exitosamente',
            'delete' => 'Religión eliminada exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Religión no encontrada',
            'cannot_delete' => 'No se puede eliminar esta religión porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
