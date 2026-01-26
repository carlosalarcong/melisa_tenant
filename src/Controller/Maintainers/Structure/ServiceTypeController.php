<?php

namespace App\Controller\Maintainers\Structure;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\ServiceType;
use App\Form\Maintainers\ServiceTypeType;
use App\Repository\Tenant\ServiceTypeRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/structure/service-type')]
class ServiceTypeController extends AbstractMantenedorController
{
    public function __construct(
        private ServiceTypeRepository $serviceTypeRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_service_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_service_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_service_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_service_type_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_service_type_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'code', 'description', 'isActive'],
            headers: ['Nombre', 'Código', 'Descripción', 'Activo'],
            filename: 'tipos_servicio_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->serviceTypeRepository->createQueryBuilder('st')
            ->orderBy('st.name', 'ASC');
    }

    protected function getColumns(): array
    {
        return ['name', 'code', 'description', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/structure/service_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return ServiceTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new ServiceType();
    }

    protected function getEntityName(): string
    {
        return 'Tipo de Servicio';
    }

    protected function getItemsPerPage(): int
    {
        return 15;
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_service_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'index' => 'Tipos de Servicio',
            'create' => 'Crear Tipo de Servicio',
            'edit' => 'Editar Tipo de Servicio',
            default => 'Tipos de Servicio'
        };
    }
}
