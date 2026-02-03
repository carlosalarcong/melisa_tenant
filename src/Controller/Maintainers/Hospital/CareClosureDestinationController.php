<?php

namespace App\Controller\Maintainers\Hospital;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\CareClosureDestination;
use App\Form\Maintainers\Hospital\CareClosureDestinationType;
use App\Repository\Tenant\CareClosureDestinationRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/hospital/care-closure-destination')]
class CareClosureDestinationController extends AbstractMantenedorController
{
    public function __construct(
        private CareClosureDestinationRepository $careClosureDestinationRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_hospital_care_closure_destination_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_hospital_care_closure_destination_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_hospital_care_closure_destination_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_hospital_care_closure_destination_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_hospital_care_closure_destination_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'isActive'],
            headers: ['Nombre', 'Activo'],
            filename: 'destinos_cierre_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->careClosureDestinationRepository->createQueryBuilder('ccd')
            ->orderBy('ccd.id', 'DESC');
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
        return 'maintainers/hospital/care_closure_destination/index.html.twig';
    }

    protected function getFormType(): string
    {
        return CareClosureDestinationType::class;
    }

    protected function createNewEntity(): object
    {
        return new CareClosureDestination();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_hospital_care_closure_destination_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Destino Cierre',
            'edit' => 'Editar Destino Cierre',
            default => 'Destinos Cierre Atencion'
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
            'create' => 'Destino de cierre creado exitosamente',
            'edit' => 'Destino de cierre actualizado exitosamente',
            'delete' => 'Destino de cierre eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Destino de cierre no encontrado',
            'cannot_delete' => 'No se puede eliminar este destino de cierre porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
