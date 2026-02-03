<?php

namespace App\Controller\Maintainers\Treasury;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\CreditCardType;
use App\Form\Maintainers\Treasury\CreditCardTypeType;
use App\Repository\Tenant\CreditCardTypeRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * CreditCardType Controller
 * 
 * Gestiona el mantenedor de Tipos de Tarjeta de Crédito
 */
#[Route('/maintainers/treasury/credit-card-type')]
class CreditCardTypeController extends AbstractMantenedorController
{
    public function __construct(
        private CreditCardTypeRepository $creditCardTypeRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService,
        TranslatorInterface $translator
    ) {
        parent::__construct($tenantEntityManager, $translator);
        $this->setExportService($exportService);
    }

    #[Route('', name: 'app_maintainers_treasury_credit_card_type_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_treasury_credit_card_type_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_treasury_credit_card_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        return $this->handleEdit($request, $id);
    }

    #[Route('/{id}/delete', name: 'app_maintainers_treasury_credit_card_type_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        return $this->handleDelete($request, $id);
    }
    
    #[Route('/export', name: 'app_maintainers_treasury_credit_card_type_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport(
            request: $request,
            columns: ['name', 'isActive'],
            headers: $this->translateColumns(['name', 'is_active']),
            filename: 'tipos_tarjeta_credito_' . date('Y-m-d') . '.csv'
        );
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->creditCardTypeRepository->createQueryBuilder('cct')
            ->orderBy('cct.id', 'DESC');
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
        return 'maintainers/treasury/credit_card_type/index.html.twig';
    }

    protected function getFormType(): string
    {
        return CreditCardTypeType::class;
    }

    protected function createNewEntity(): object
    {
        return new CreditCardType();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_treasury_credit_card_type_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Tipo Tarjeta Crédito',
            'edit' => 'Editar Tipo Tarjeta Crédito',
            default => 'Tipos de Tarjeta de Credito'
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
            'create' => 'Tipo de tarjeta de crédito creado exitosamente',
            'edit' => 'Tipo de tarjeta de crédito actualizado exitosamente',
            'delete' => 'Tipo de tarjeta de crédito eliminado exitosamente',
            default => 'Operación completada exitosamente'
        };
    }

    protected function getErrorMessage(string $type): string
    {
        return match($type) {
            'not_found' => 'Tipo de tarjeta de crédito no encontrado',
            'cannot_delete' => 'No se puede eliminar este tipo de tarjeta de crédito porque está en uso',
            default => 'Ha ocurrido un error'
        };
    }
}
