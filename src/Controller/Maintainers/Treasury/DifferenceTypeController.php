<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\DifferenceType;
use App\Form\Maintainers\Treasury\DifferenceTypeType;
use App\Repository\Tenant\DifferenceTypeRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * DifferenceType Controller
 * 
 * Gestiona el mantenedor de Tipos de Diferencia
 */
#[Route('/maintainers/treasury/difference-type')]
class DifferenceTypeController extends AbstractMantenedorController
{
    public function __construct(
        private DifferenceTypeRepository $differenceTypeRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_treasury_difference_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_difference_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_difference_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_difference_type_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_treasury_difference_type_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'description', 'differenceDirection.name', 'isActive'],
            headers: ['Nombre', 'Descripcion', 'Sentido', 'Activo'],
            filename: 'tipos_diferencia_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->differenceTypeRepository->createQueryBuilder('dt')
            ->leftJoin('dt.differenceDirection', 'dd')
            ->addSelect('dd')
            ->orderBy('dt.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return ['name', 'description', 'differenceDirection.name', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/treasury/difference_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return DifferenceTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new DifferenceType();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_treasury_difference_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Tipo Diferencia',
            'edit' => 'Editar Tipo Diferencia',
            default => 'Tipos de Diferencia'
        };
    }

    protected function beforeSave(object $entity, Request $request): void
    {
        $entity->setUpdatedAt(new \DateTime());
    }

    protected function canDelete(object $entity): bool
    {
        return true;
    }

    protected function getSuccessMessage(string $action): string
    {
        return match($action) {
            'create' => 'Tipo de diferencia creado exitosamente',
            'edit' => 'Tipo de diferencia actualizado exitosamente',
            'delete' => 'Tipo de diferencia eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Tipo de diferencia no encontrado',
            'cannot_delete' => 'No se puede eliminar este tipo de diferencia porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
