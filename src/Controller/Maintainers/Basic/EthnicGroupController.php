<?php

namespace App\Controller\Maintainers\Basic;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\EthnicGroup;
use App\Form\Maintainers\Personal\EthnicGroupType;
use App\Repository\Tenant\EthnicGroupRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Ethnic Group Controller
 * 
 * Gestiona el mantenedor de Pueblos Originarios
 */
#[Route('/maintainers/basic/ethnic-group')]
class EthnicGroupController extends AbstractMantenedorController
{
    public function __construct(
        private EthnicGroupRepository $repository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_ethnic_group_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_ethnic_group_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_ethnic_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_ethnic_group_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_ethnic_group_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'code', 'isActive'],
            headers: ['Nombre', 'Código', 'Activo'],
            filename: 'pueblos_originarios_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->repository->createQueryBuilder('eg')
            ->orderBy('eg.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return ['name', 'code', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/basic/ethnic_group/index.html.twig';
    }

    protected function getFormType(): string
    {
        return EthnicGroupType::class;
    }

    protected function createNewEntity(): object
    {
        return new EthnicGroup();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_ethnic_group_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Pueblo Originario',
            'edit' => 'Editar Pueblo Originario',
            default => 'Pueblos Originarios'
        };
    }

    protected function findEntity(int $id): ?object
    {
        return $this->repository->find($id);
    }
}
