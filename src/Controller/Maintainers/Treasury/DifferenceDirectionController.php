<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\DifferenceDirection;
use App\Form\Maintainers\Treasury\DifferenceDirectionType;
use App\Repository\Tenant\DifferenceDirectionRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * DifferenceDirection Controller
 * 
 * Gestiona el mantenedor de Sentidos de Diferencia
 */
#[Route('/maintainers/treasury/difference-direction')]
class DifferenceDirectionController extends AbstractMantenedorController
{
    public function __construct(
        private DifferenceDirectionRepository $differenceDirectionRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService,
        TranslatorInterface $translator
    ) {
        parent::__construct($tenantEntityManager, $translator);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_treasury_difference_direction_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_difference_direction_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_difference_direction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_difference_direction_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_treasury_difference_direction_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'isActive'],
            headers: ['Nombre', 'Activo'],
            filename: 'sentidos_diferencia_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->differenceDirectionRepository->createQueryBuilder('dd')
            ->orderBy('dd.id', 'DESC');
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
        return 'maintainers/treasury/difference_direction/index.html.twig';
    }

    protected function getFormType(): string
    {
        return DifferenceDirectionType::class;
    }

    protected function createNewEntity(): object
    {
        return new DifferenceDirection();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_treasury_difference_direction_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Sentido Diferencia',
            'edit' => 'Editar Sentido Diferencia',
            default => 'Sentidos de Diferencia'
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
            'create' => 'Sentido de diferencia creado exitosamente',
            'edit' => 'Sentido de diferencia actualizado exitosamente',
            'delete' => 'Sentido de diferencia eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Sentido de diferencia no encontrado',
            'cannot_delete' => 'No se puede eliminar este sentido de diferencia porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
