<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\CashRegisterLocation;
use App\Form\Maintainers\Treasury\CashRegisterLocationType;
use App\Repository\Tenant\CashRegisterLocationRepository;
use App\Service\Export\ExportService;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/treasury/cash-register-location')]
class CashRegisterLocationController extends AbstractMantenedorController
{
    public function __construct(
        private readonly CashRegisterLocationRepository $repository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('/', name: 'app_maintainers_treasury_cash_register_location_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_cash_register_location_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_cash_register_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_cash_register_location_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }

    #[Route('/export', name: 'app_maintainers_treasury_cash_register_location_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        $exportColumns = $this->getExportColumns();
        return $this->handleExport(
            request: $request,
            columns: array_keys($exportColumns),
            headers: array_values($exportColumns),
            filename: $this->getExportFileName()
        );
    }

    protected function getEntityClass(): string
    {
        return CashRegisterLocation::class;
    }

    protected function getFormType(): string
    {
        return CashRegisterLocationType::class;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/treasury/cash_register_location';
    }

    protected function getRoutePrefix(): string
    {
        return 'app_maintainers_treasury_cash_register_location';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Ubicación de Caja',
            'edit' => 'Editar Ubicación de Caja',
            default => 'Ubicaciones de Caja'
        };
    }

    protected function getColumns(): array
    {
        return ['id', 'name', 'branch.name', 'isActive'];
    }

    protected function createNewEntity(): object
    {
        return new CashRegisterLocation();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_treasury_cash_register_location_index';
    }

    protected function getData(Request $request): array
    {
        $qb = $this->repository->createQueryBuilder('crl')
            ->leftJoin('crl.branch', 'b')
            ->addSelect('b');

        $search = $request->query->get('search', '');
        if (!empty($search)) {
            $qb->andWhere('crl.name LIKE :search OR crl.description LIKE :search OR b.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('crl.name', 'ASC');

        return [
            'query' => $qb->getQuery(),
            'searchTerm' => $search
        ];
    }

    protected function getExportColumns(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'description' => 'Descripción',
            'branch.name' => 'Sucursal',
            'isActive' => 'Activo'
        ];
    }

    protected function getExportFileName(): string
    {
        return 'ubicaciones_caja_' . date('Y-m-d_His') . '.csv';
    }
}
