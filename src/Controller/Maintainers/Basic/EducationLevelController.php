<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\EducationLevel;
use App\Form\Maintainers\Education\EducationLevelType;
use App\Repository\Tenant\EducationLevelRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Education Level Controller
 * 
 * Gestiona el mantenedor de Niveles de Instrucción
 */
#[Route('/maintainers/basic/education-level')]
class EducationLevelController extends AbstractMantenedorController
{
    public function __construct(
        private EducationLevelRepository $repository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_education_level_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_education_level_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_education_level_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_education_level_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_education_level_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'isActive'],
            headers: ['Nombre', 'Activo'],
            filename: 'niveles_educacion_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->repository->createQueryBuilder('el')
            ->orderBy('el.id', 'DESC');
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
        return 'maintainers/basic/education_level/index.html.twig';
    }

    protected function getFormType(): string
    {
        return EducationLevelType::class;
    }

    protected function createNewEntity(): object
    {
        return new EducationLevel();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_education_level_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Nivel de Instrucción',
            'edit' => 'Editar Nivel de Instrucción',
            default => 'Niveles de Instrucción'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
