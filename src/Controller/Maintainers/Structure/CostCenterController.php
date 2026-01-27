<?php

namespace App\Controller\Maintainers\Structure;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\CostCenter;
use App\Form\Maintainers\Organizational\CostCenterType;
use App\Repository\Tenant\CostCenterRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/structure/cost-center')]
class CostCenterController extends AbstractMantenedorController
{
    public function __construct(
        private CostCenterRepository $costCenterRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_cost_center_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_cost_center_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_cost_center_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_cost_center_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_cost_center_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'code', 'description', 'isActive'],
            headers: ['Nombre', 'Código', 'Descripción', 'Activo'],
            filename: 'centros_costo_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->costCenterRepository->createQueryBuilder('cc')
            ->orderBy('cc.name', 'ASC');
    }

    protected function getColumns(): array
    {
        return ['name', 'code', 'description', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/structure/cost_center/index.html.twig';
    }

    protected function getFormType(): string
    {
        return CostCenterType::class;
    }

    protected function createNewEntity(): object
    {
        return new CostCenter();
    }

    protected function getEntityName(): string
    {
        return 'Centro de Costo';
    }

    protected function getItemsPerPage(): int
    {
        return 15;
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_cost_center_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'index' => 'Centros de Costo',
            'create' => 'Crear Centro de Costo',
            'edit' => 'Editar Centro de Costo',
            default => 'Centros de Costo'
        };
    }
}
