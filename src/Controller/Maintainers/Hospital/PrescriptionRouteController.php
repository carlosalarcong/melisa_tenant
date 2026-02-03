<?php

namespace App\Controller\Maintainers\Hospital;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\PrescriptionRoute;
use App\Form\Maintainers\Hospital\PrescriptionRouteType;
use App\Repository\Tenant\PrescriptionRouteRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/maintainers/hospital/prescription-route')]
class PrescriptionRouteController extends AbstractMantenedorController
{
    public function __construct(
        private PrescriptionRouteRepository $prescriptionRouteRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService,
        TranslatorInterface $translator
    ) {
        parent::__construct($tenantEntityManager, $translator);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_hospital_prescription_route_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_hospital_prescription_route_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_hospital_prescription_route_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_hospital_prescription_route_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_hospital_prescription_route_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'sortOrder', 'isActive'],
            headers: $this->translateColumns(['name', 'order', 'is_active']),
            filename: 'vias_administracion_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->prescriptionRouteRepository->createQueryBuilder('pr')
            ->orderBy('pr.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
            'name' => 'Nombre',
            'sortOrder' => 'Orden',
            'isActive' => 'Estado'
        ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/hospital/prescription_route/index.html.twig';
    }

    protected function getFormType(): string
    {
        return PrescriptionRouteType::class;
    }

    protected function createNewEntity(): object
    {
        return new PrescriptionRoute();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_hospital_prescription_route_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Via',
            'edit' => 'Editar Via',
            default => 'Vias de Administracion'
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
            'create' => 'Via de administración creada exitosamente',
            'edit' => 'Via de administración actualizada exitosamente',
            'delete' => 'Via de administración eliminada exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Via de administración no encontrada',
            'cannot_delete' => 'No se puede eliminar esta via de administración porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
