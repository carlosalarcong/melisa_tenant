<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\EducationLevelDetail;
use App\Form\Maintainers\EducationLevelDetailType;
use App\Repository\Tenant\EducationLevelDetailRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Education Level Detail Controller
 * 
 * Gestiona el mantenedor de Detalles de Nivel de Instrucción
 */
#[Route('/maintainers/basic/education-level-detail')]
class EducationLevelDetailController extends AbstractMantenedorController
{
    public function __construct(
        private EducationLevelDetailRepository $repository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_education_level_detail_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_education_level_detail_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_education_level_detail_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_education_level_detail_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }

    #[Route('/export', name: 'app_maintainers_education_level_detail_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'educationLevel', 'isActive'],
            headers: ['Nombre', 'Nivel de Instrucción', 'Activo'],
            filename: 'detalles_nivel_instruccion_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->repository->createQueryBuilder('eld')
            ->orderBy('eld.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return ['name', 'educationLevel', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/basic/education_level_detail/index.html.twig';
    }

    protected function getFormType(): string
    {
        return EducationLevelDetailType::class;
    }

    protected function createNewEntity(): object
    {
        return new EducationLevelDetail();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_education_level_detail_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Detalle de Nivel de Instrucción',
            'edit' => 'Editar Detalle de Nivel de Instrucción',
            default => 'Detalles de Nivel de Instrucción'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
