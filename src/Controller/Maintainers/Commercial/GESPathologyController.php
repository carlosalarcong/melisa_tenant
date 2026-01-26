<?php

namespace App\Controller\Maintainers\Commercial;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\GESPathology;
use App\Form\Maintainers\Commercial\GESPathologyType;
use App\Repository\Tenant\GESPathologyRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/commercial/ges-pathology')]
class GESPathologyController extends AbstractMantenedorController
{
    public function __construct(
        private GESPathologyRepository $gesPathologyRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->gesPathologyRepository->createQueryBuilder('gp')
            ->orderBy('gp.pathologyNumber', 'ASC');
    }

    protected function getColumns(): array
    {
        return [
            'id' => 'ID',
            'pathologyNumber' => 'Número',
            'name' => 'Nombre',
            'minAge' => 'Edad Mín',
            'maxAge' => 'Edad Máx',
            'genderRestriction' => 'Género',
            'guaranteedDays' => 'Días Garantía',
            'isActive' => 'Activo'
        ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/commercial/ges_pathology/index.html.twig';
    }

    protected function getFormType(): string
    {
        return GESPathologyType::class;
    }

    protected function createNewEntity(): object
    {
        return new GESPathology();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_commercial_ges_pathology_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Patología GES',
            'edit' => 'Editar Patología GES',
            default => 'Patologías GES'
        };
    }

    #[Route('', name: 'app_maintainers_commercial_ges_pathology_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_commercial_ges_pathology_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_commercial_ges_pathology_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GESPathology $gesPathology): Response
    {
        return $this->handleEdit($request, $gesPathology->getId());
    }

    #[Route('/{id}', name: 'app_maintainers_commercial_ges_pathology_delete', methods: ['DELETE'])]
    public function delete(Request $request, GESPathology $gesPathology): Response
    {
        return $this->handleDelete($request, $gesPathology->getId());
    }

    #[Route('/export', name: 'app_maintainers_commercial_ges_pathology_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport($request);
    }
}
