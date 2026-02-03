<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\CurrencyType;
use App\Form\Maintainers\Treasury\CurrencyTypeType;
use App\Repository\Tenant\CurrencyTypeRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * CurrencyType Controller
 * 
 * Gestiona el mantenedor de Tipos de Moneda
 */
#[Route('/maintainers/treasury/currency-type')]
class CurrencyTypeController extends AbstractMantenedorController
{
    public function __construct(
        private CurrencyTypeRepository $currencyTypeRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_treasury_currency_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_currency_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_currency_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_currency_type_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_treasury_currency_type_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'isClp', 'isActive'],
            headers: ['Nombre', 'Es CLP', 'Activo'],
            filename: 'tipos_moneda_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->currencyTypeRepository->createQueryBuilder('ct')
            ->orderBy('ct.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
        'name' => 'Nombre',
        'isClp' => 'CLP',
        'isActive' => 'Estado'
    ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/treasury/currency_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return CurrencyTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new CurrencyType();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_treasury_currency_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Tipo Moneda',
            'edit' => 'Editar Tipo Moneda',
            default => 'Tipos de Moneda'
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
            'create' => 'Tipo de moneda creado exitosamente',
            'edit' => 'Tipo de moneda actualizado exitosamente',
            'delete' => 'Tipo de moneda eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Tipo de moneda no encontrado',
            'cannot_delete' => 'No se puede eliminar este tipo de moneda porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
