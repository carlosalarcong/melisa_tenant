<?php

namespace App\Controller\Maintainers\Hospital;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\IntoxicationState;
use App\Form\Maintainers\Hospital\IntoxicationStateType;
use App\Repository\Tenant\IntoxicationStateRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/hospital/intoxication-state')]
class IntoxicationStateController extends AbstractMantenedorController
{
    public function __construct(
        private IntoxicationStateRepository $intoxicationStateRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_hospital_intoxication_state_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_hospital_intoxication_state_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_hospital_intoxication_state_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_hospital_intoxication_state_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_hospital_intoxication_state_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'isActive'],
            headers: ['Nombre', 'Activo'],
            filename: 'estados_ebriedad_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->intoxicationStateRepository->createQueryBuilder('is2')
            ->orderBy('is2.id', 'DESC');
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
        return 'maintainers/hospital/intoxication_state/index.html.twig';
    }

    protected function getFormType(): string
    {
        return IntoxicationStateType::class;
    }

    protected function createNewEntity(): object
    {
        return new IntoxicationState();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_hospital_intoxication_state_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Estado de Ebriedad',
            'edit' => 'Editar Estado de Ebriedad',
            default => 'Estados de Ebriedad'
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
            'create' => 'Estado de ebriedad creado exitosamente',
            'edit' => 'Estado de ebriedad actualizado exitosamente',
            'delete' => 'Estado de ebriedad eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Estado de ebriedad no encontrado',
            'cannot_delete' => 'No se puede eliminar este estado de ebriedad porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
