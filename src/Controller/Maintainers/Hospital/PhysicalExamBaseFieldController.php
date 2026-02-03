<?php

namespace App\Controller\Maintainers\Hospital;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\PhysicalExamBaseField;
use App\Form\Maintainers\Hospital\PhysicalExamBaseFieldType;
use App\Repository\Tenant\PhysicalExamBaseFieldRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/hospital/physical-exam-base-field')]
class PhysicalExamBaseFieldController extends AbstractMantenedorController
{
    public function __construct(
        private PhysicalExamBaseFieldRepository $physicalExamBaseFieldRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_hospital_physical_exam_base_field_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_hospital_physical_exam_base_field_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_hospital_physical_exam_base_field_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_hospital_physical_exam_base_field_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_hospital_physical_exam_base_field_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'sortOrder', 'fieldType', 'isRequired', 'isActive'],
            headers: ['Nombre', 'Orden', 'Tipo Campo', 'Obligatorio', 'Activo'],
            filename: 'campos_base_examen_fisico_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->physicalExamBaseFieldRepository->createQueryBuilder('pebf')
            ->orderBy('pebf.sortOrder', 'ASC');
    }

    protected function getColumns(): array
    {
        return [
            'name' => 'Nombre',
            'sortOrder' => 'Orden',
            'fieldType' => 'Tipo Campo',
            'isRequired' => 'Obligatorio',
            'isActive' => 'Estado'
        ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/hospital/physical_exam_base_field/index.html.twig';
    }

    protected function getFormType(): string
    {
        return PhysicalExamBaseFieldType::class;
    }

    protected function createNewEntity(): object
    {
        return new PhysicalExamBaseField();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_hospital_physical_exam_base_field_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Campo Base',
            'edit' => 'Editar Campo Base',
            default => 'Campos Base Examen Fisico'
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
            'create' => 'Campo base de examen físico creado exitosamente',
            'edit' => 'Campo base de examen físico actualizado exitosamente',
            'delete' => 'Campo base de examen físico eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Campo base de examen físico no encontrado',
            'cannot_delete' => 'No se puede eliminar este campo base porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
