<?php

namespace App\Controller\Maintainers\Commercial;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\ENOPathology;
use App\Form\Maintainers\Commercial\ENOPathologyType;
use App\Repository\Tenant\ENOPathologyRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/commercial/eno-pathology')]
class ENOPathologyController extends AbstractMantenedorController
{
    public function __construct(
        private ENOPathologyRepository $enoPathologyRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->enoPathologyRepository->createQueryBuilder('ep')
            ->orderBy('ep.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
        'id' => 'Id',
        'code' => 'Código',
        'name' => 'Nombre',
        'icd10Code' => 'Icd10Code',
        'requiresSpecialist' => 'RequiresSpecialist',
        'isChronic' => 'IsChronic',
        'isActive' => 'Estado'
    ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/commercial/eno_pathology/index.html.twig';
    }

    protected function getFormType(): string
    {
        return ENOPathologyType::class;
    }

    protected function createNewEntity(): object
    {
        return new ENOPathology();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_commercial_eno_pathology_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Patología ENO',
            'edit' => 'Editar Patología ENO',
            default => 'Patologías ENO'
        };
    }

    #[Route('', name: 'app_maintainers_commercial_eno_pathology_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_commercial_eno_pathology_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_commercial_eno_pathology_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ENOPathology $enoPathology): Response
    {
        return $this->handleEdit($request, $enoPathology->getId());
    }

    #[Route('/{id}', name: 'app_maintainers_commercial_eno_pathology_delete', methods: ['DELETE'])]
    public function delete(Request $request, ENOPathology $enoPathology): Response
    {
        return $this->handleDelete($request, $enoPathology->getId());
    }

    #[Route('/export', name: 'app_maintainers_commercial_eno_pathology_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport($request);
    }
}
