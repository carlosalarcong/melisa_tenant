<?php

namespace App\Controller\Maintainers\Hospital;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\ClinicalActionCategory;
use App\Form\Maintainers\Hospital\ClinicalActionCategoryType;
use App\Repository\Tenant\ClinicalActionCategoryRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/hospital/clinical-action-category')]
class ClinicalActionCategoryController extends AbstractMantenedorController
{
    public function __construct(
        private ClinicalActionCategoryRepository $clinicalActionCategoryRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_hospital_clinical_action_category_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_hospital_clinical_action_category_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_hospital_clinical_action_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_hospital_clinical_action_category_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_hospital_clinical_action_category_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'isActive'],
            headers: ['Nombre', 'Activo'],
            filename: 'categorias_accion_clinica_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->clinicalActionCategoryRepository->createQueryBuilder('cac')
            ->orderBy('cac.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
            'name' => 'Nombre',
            'isActive' => 'Estado'
        ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/hospital/clinical_action_category/index.html.twig';
    }

    protected function getFormType(): string
    {
        return ClinicalActionCategoryType::class;
    }

    protected function createNewEntity(): object
    {
        return new ClinicalActionCategory();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_hospital_clinical_action_category_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Categoria AC',
            'edit' => 'Editar Categoria AC',
            default => 'Categorias Accion Clinica'
        };
    }

    protected function beforeSave(object $entity, Request $request): void
    {
        $entity->setUpdatedAt(new \DateTime());
    }

    protected function canDelete(object $entity): bool
    {
        return true;
    }

    protected function getSuccessMessage(string $action): string
    {
        return match($action) {
            'create' => 'Categoría de acción clínica creada exitosamente',
            'edit' => 'Categoría de acción clínica actualizada exitosamente',
            'delete' => 'Categoría de acción clínica eliminada exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Categoría de acción clínica no encontrada',
            'cannot_delete' => 'No se puede eliminar esta categoría de acción clínica porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
