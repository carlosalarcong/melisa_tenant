<?php

namespace App\Controller\Maintainers\Surgery;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\AnesthesiaType;
use App\Form\Maintainers\Surgery\AnesthesiaTypeType;
use App\Repository\Tenant\AnesthesiaTypeRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/surgery/anesthesia-type')]
class AnesthesiaTypeController extends AbstractMantenedorController
{
    public function __construct(
        private readonly AnesthesiaTypeRepository $repository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager, $exportService);
    }

    #[Route('', name: 'app_maintainers_surgery_anesthesia_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_surgery_anesthesia_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_surgery_anesthesia_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_surgery_anesthesia_type_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }

    #[Route('/export', name: 'app_maintainers_surgery_anesthesia_type_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport($request);
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_surgery_anesthesia_type_index';
    }

    protected function getEntityClass(): string
    {
        return AnesthesiaType::class;
    }

    protected function getFormType(): string
    {
        return AnesthesiaTypeType::class;
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/surgery/anesthesia_type/index.html.twig';
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->repository->createQueryBuilder('at')
            ->orderBy('at.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
            'name' => 'Nombre',
            'description' => 'Descripción',
            'isActive' => 'Estado'
        ];
    }

    protected function createNewEntity(): object
    {
        return new AnesthesiaType();
    }

    protected function getPageTitle(?string $action = null): string
    {
        return match($action) {
            'create' => 'Crear Tipo de Anestesia',
            'edit' => 'Editar Tipo de Anestesia',
            default => 'Tipos de Anestesia'
        };
    }

    protected function getExportColumns(): array
    {
        return ['name', 'description', 'isActive'];
    }

    protected function getExportHeaders(): array
    {
        return ['Nombre', 'Descripción', 'Activo'];
    }

    protected function getExportFileName(): string
    {
        return 'tipos_anestesia_' . date('Y-m-d') . '.csv';
    }
}
