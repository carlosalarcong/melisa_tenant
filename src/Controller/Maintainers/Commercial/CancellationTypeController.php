<?php

namespace App\Controller\Maintainers\Commercial;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\CancellationType;
use App\Form\Maintainers\Commercial\CancellationTypeType;
use App\Repository\Tenant\CancellationTypeRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/commercial/cancellation-type')]
class CancellationTypeController extends AbstractMantenedorController
{
    public function __construct(
        private CancellationTypeRepository $cancellationTypeRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->cancellationTypeRepository->createQueryBuilder('ct')
            ->orderBy('ct.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Nombre',
            'code' => 'Código',
            'description' => 'Descripción',
            'isActive' => 'Activo'
        ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/commercial/cancellation_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return CancellationTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new CancellationType();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_commercial_cancellation_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Tipo de Anulación',
            'edit' => 'Editar Tipo de Anulación',
            default => 'Tipos de Anulación'
        };
    }

    #[Route('', name: 'app_maintainers_commercial_cancellation_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_commercial_cancellation_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_commercial_cancellation_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CancellationType $cancellationType): Response
    {
        return $this->handleEdit($request, $cancellationType->getId());
    }

    #[Route('/{id}', name: 'app_maintainers_commercial_cancellation_type_delete', methods: ['DELETE'])]
    public function delete(Request $request, CancellationType $cancellationType): Response
    {
        return $this->handleDelete($request, $cancellationType->getId());
    }

    #[Route('/export', name: 'app_maintainers_commercial_cancellation_type_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport($request);
    }
}
