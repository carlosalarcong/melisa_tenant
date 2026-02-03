<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\GratuityReason;
use App\Form\Maintainers\Treasury\GratuityReasonType;
use App\Repository\Tenant\GratuityReasonRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/maintainers/treasury/gratuity-reason')]
class GratuityReasonController extends AbstractMantenedorController
{
    public function __construct(
        private GratuityReasonRepository $gratuityReasonRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService,
        TranslatorInterface $translator
    ) {
        parent::__construct($tenantEntityManager, $translator);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_treasury_gratuity_reason_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_gratuity_reason_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_gratuity_reason_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_gratuity_reason_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_treasury_gratuity_reason_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'gratuityType.name', 'branch.name', 'isActive'],
            headers: ['Nombre', 'Tipo Gratuidad', 'Sucursal', 'Activo'],
            filename: 'motivos_gratuidad_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->gratuityReasonRepository->createQueryBuilder('gr')
            ->leftJoin('gr.gratuityType', 'gt')
            ->addSelect('gt')
            ->leftJoin('gr.branch', 'b')
            ->addSelect('b')
            ->orderBy('gr.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
        'name' => 'Nombre',
        'gratuityType.name' => 'Tipo Gratuidad',
        'branch.name' => 'Sucursal',
        'isActive' => 'Estado'
    ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/treasury/gratuity_reason/index.html.twig';
    }

    protected function getFormType(): string
    {
        return GratuityReasonType::class;
    }

    protected function createNewEntity(): object
    {
        return new GratuityReason();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_treasury_gratuity_reason_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Motivo Gratuidad',
            'edit' => 'Editar Motivo Gratuidad',
            default => 'Motivos de Gratuidad'
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
            'create' => 'Motivo de gratuidad creado exitosamente',
            'edit' => 'Motivo de gratuidad actualizado exitosamente',
            'delete' => 'Motivo de gratuidad eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Motivo de gratuidad no encontrado',
            'cannot_delete' => 'No se puede eliminar este motivo de gratuidad porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
