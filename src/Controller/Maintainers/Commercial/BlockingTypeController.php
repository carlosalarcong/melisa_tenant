<?php

namespace App\Controller\Maintainers\Commercial;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\BlockingType;
use App\Form\Maintainers\Commercial\BlockingTypeType;
use App\Repository\Tenant\BlockingTypeRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/commercial/blocking-type')]
class BlockingTypeController extends AbstractMantenedorController
{
    public function __construct(
        private BlockingTypeRepository $blockingTypeRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->blockingTypeRepository->createQueryBuilder('bt')
            ->orderBy('bt.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
        'id' => 'Id',
        'name' => 'Nombre',
        'code' => 'Código',
        'description' => 'Descripción',
        'isActive' => 'Estado'
    ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/commercial/blocking_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return BlockingTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new BlockingType();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_commercial_blocking_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Tipo de Bloqueo',
            'edit' => 'Editar Tipo de Bloqueo',
            default => 'Tipos de Bloqueo'
        };
    }

    #[Route('', name: 'app_maintainers_commercial_blocking_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_commercial_blocking_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_commercial_blocking_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BlockingType $blockingType): Response
    {
        return $this->handleEdit($request, $blockingType->getId());
    }

    #[Route('/{id}', name: 'app_maintainers_commercial_blocking_type_delete', methods: ['DELETE'])]
    public function delete(Request $request, BlockingType $blockingType): Response
    {
        return $this->handleDelete($request, $blockingType->getId());
    }

    #[Route('/export', name: 'app_maintainers_commercial_blocking_type_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport($request);
    }
}
