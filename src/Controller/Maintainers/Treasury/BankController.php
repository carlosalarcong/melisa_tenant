<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\Bank;
use App\Form\Maintainers\Treasury\BankType;
use App\Repository\Tenant\BankRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Bank Controller
 * 
 * Gestiona el mantenedor de Bancos
 */
#[Route('/maintainers/treasury/bank')]
class BankController extends AbstractMantenedorController
{
    public function __construct(
        private BankRepository $bankRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_treasury_bank_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_bank_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_bank_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_bank_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_treasury_bank_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['rut', 'name', 'currentAccount', 'isActive'],
            headers: ['RUT', 'Nombre', 'Cuenta Corriente', 'Activo'],
            filename: 'bancos_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->bankRepository->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return ['rut', 'name', 'currentAccount', 'isActive'];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/treasury/bank/index.html.twig';
    }

    protected function getFormType(): string
    {
        return BankType::class;
    }

    protected function createNewEntity(): object
    {
        return new Bank();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_treasury_bank_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Banco',
            'edit' => 'Editar Banco',
            default => 'Bancos'
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
            'create' => 'Banco creado exitosamente',
            'edit' => 'Banco actualizado exitosamente',
            'delete' => 'Banco eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Banco no encontrado',
            'cannot_delete' => 'No se puede eliminar este banco porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
