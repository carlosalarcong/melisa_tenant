<?php

namespace App\Controller\Maintainers\Hospital;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\PrescriptionType;
use App\Form\Maintainers\Hospital\PrescriptionTypeType;
use App\Repository\Tenant\PrescriptionTypeRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/hospital/prescription-type')]
class PrescriptionTypeController extends AbstractMantenedorController
{
    public function __construct(
        private PrescriptionTypeRepository $prescriptionTypeRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_hospital_prescription_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_hospital_prescription_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_hospital_prescription_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_hospital_prescription_type_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_hospital_prescription_type_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'isActive'],
            headers: ['Nombre', 'Activo'],
            filename: 'tipos_receta_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->prescriptionTypeRepository->createQueryBuilder('pt')
            ->orderBy('pt.id', 'DESC');
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
        return 'maintainers/hospital/prescription_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return PrescriptionTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new PrescriptionType();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_hospital_prescription_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Tipo Receta',
            'edit' => 'Editar Tipo Receta',
            default => 'Tipos de Receta'
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
            'create' => 'Tipo de receta creado exitosamente',
            'edit' => 'Tipo de receta actualizado exitosamente',
            'delete' => 'Tipo de receta eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Tipo de receta no encontrado',
            'cannot_delete' => 'No se puede eliminar este tipo de receta porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
